<?php

namespace Tests\Unit\Domain\Transaction\Services;

use App\Domain\Setting\Models\Setting;
use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transaction\Models\TransactionStatus;
use App\Domain\Transaction\Services\TransferService;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\ExternalWallet;
use App\Domain\Wallet\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransferServiceTest extends TestCase
{
    use RefreshDatabase;

    private TransferService $transferService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transferService = app(TransferService::class);

        // Seed statuses if not present
        if (TransactionStatus::count() === 0) {
            TransactionStatus::create(['name' => 'Pending', 'code' => 'pending']);
            TransactionStatus::create(['name' => 'Completed', 'code' => 'completed']);
            TransactionStatus::create(['name' => 'Rejected', 'code' => 'rejected']);
            TransactionStatus::create(['name' => 'Cancelled', 'code' => 'cancelled']);
        }

        // Ensure settings exist
        Setting::updateOrCreate(['key' => 'transfer_limit'], ['value' => '1000', 'type' => 'number']);
    }

    private function fundWallet(Wallet $wallet, float $amount)
    {
        // Use the wallet's currency to avoid creating a new one (and potential collisions)
        $fromWallet = Wallet::factory()->create([
            'currency_id' => $wallet->currency_id
        ]);

        Transaction::factory()->create([
            'from_wallet_id' => $fromWallet->id,
            'to_wallet_id' => $wallet->id,
            'amount' => $amount,
            'transaction_status_id' => TransactionStatus::where('code', 'completed')->first()->id,
            'type' => 'credit'
        ]);
    }

    /** @test */
    public function it_initiates_transfer_successfully_as_user_pending()
    {
        $role = \App\Domain\User\Models\UserRole::firstOrCreate(['name' => 'user'], ['label' => 'User']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $currency = \App\Domain\Currency\Models\Currency::factory()->create();
        $wallet = Wallet::factory()->create([
            'currency_id' => $currency->id
        ]);
        $user->wallets()->attach($wallet);
        $this->fundWallet($wallet, 1500);

        $externalWallet = ExternalWallet::factory()->create([
            'currency_id' => $currency->id
        ]);

        $transaction = $this->transferService->initiateTransfer($wallet, 'external', $externalWallet->address, 1100, $user);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'user_id' => $user->id,
            'from_wallet_id' => $wallet->id,
            'external_wallet_id' => $externalWallet->id,
            'amount' => 1100,
            'transaction_status_id' => TransactionStatus::where('code', 'pending')->first()->id
        ]);

        $this->assertEquals(400, $wallet->fresh()->available_balance);
    }

    /** @test */
    public function it_auto_approves_transfer_as_user_within_limit()
    {
        $role = \App\Domain\User\Models\UserRole::firstOrCreate(['name' => 'user'], ['label' => 'User']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $currency = \App\Domain\Currency\Models\Currency::factory()->create();
        $wallet = Wallet::factory()->create(['currency_id' => $currency->id]);
        $user->wallets()->attach($wallet);
        $this->fundWallet($wallet, 500);

        $externalWallet = ExternalWallet::factory()->create(['currency_id' => $currency->id]);

        $transaction = $this->transferService->initiateTransfer($wallet, 'external', $externalWallet->address, 100, $user);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'user_id' => $user->id,
            'transaction_status_id' => TransactionStatus::where('code', 'completed')->first()->id
        ]);

        $this->assertEquals(400, $wallet->fresh()->balance);
    }

    /** @test */
    public function it_auto_approves_transfer_as_manager()
    {
        $role = \App\Domain\User\Models\UserRole::firstOrCreate(['name' => 'manager'], ['label' => 'Manager']);
        $manager = User::factory()->create(['role_id' => $role->id]);

        $currency = \App\Domain\Currency\Models\Currency::factory()->create();
        $wallet = Wallet::factory()->create(['currency_id' => $currency->id]);
        $manager->wallets()->attach($wallet);
        $this->fundWallet($wallet, 5000);

        $externalWallet = ExternalWallet::factory()->create(['currency_id' => $currency->id]);

        $transaction = $this->transferService->initiateTransfer($wallet, 'external', $externalWallet->address, 2000, $manager);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'user_id' => $manager->id,
            'approved_by' => $manager->id,
            'transaction_status_id' => TransactionStatus::where('code', 'completed')->first()->id
        ]);
    }

    /** @test */
    public function it_fails_initiate_transfer_insufficient_funds()
    {
        $role = \App\Domain\User\Models\UserRole::firstOrCreate(['name' => 'user'], ['label' => 'User']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $currency = \App\Domain\Currency\Models\Currency::factory()->create();
        $wallet = Wallet::factory()->create(['currency_id' => $currency->id]);
        $user->wallets()->attach($wallet);
        $this->fundWallet($wallet, 50);

        $externalWallet = ExternalWallet::factory()->create(['currency_id' => $currency->id]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient funds');

        $this->transferService->initiateTransfer($wallet, 'external', $externalWallet->address, 100, $user);
    }

    /** @test */
    public function it_fails_initiate_transfer_currency_mismatch()
    {
        $role = \App\Domain\User\Models\UserRole::firstOrCreate(['name' => 'user'], ['label' => 'User']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $currency1 = \App\Domain\Currency\Models\Currency::factory()->create();
        $wallet = Wallet::factory()->create(['currency_id' => $currency1->id]);
        $user->wallets()->attach($wallet);
        $this->fundWallet($wallet, 500);

        $currency2 = \App\Domain\Currency\Models\Currency::factory()->create(['code' => 'MIS_MATCH']);
        $externalWallet = ExternalWallet::factory()->create(['currency_id' => $currency2->id]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Currency mismatch');

        $this->transferService->initiateTransfer($wallet, 'external', $externalWallet->address, 100, $user);
    }

    /** @test */
    public function it_approves_transfer_successfully()
    {
        $userRole = \App\Domain\User\Models\UserRole::firstOrCreate(['name' => 'user'], ['label' => 'User']);
        $user = User::factory()->create(['role_id' => $userRole->id]);

        $managerRole = \App\Domain\User\Models\UserRole::firstOrCreate(['name' => 'manager'], ['label' => 'Manager']);
        $manager = User::factory()->create(['role_id' => $managerRole->id]);

        $currency = \App\Domain\Currency\Models\Currency::factory()->create(['code' => 'APP_TRF']);
        $wallet = Wallet::factory()->create(['currency_id' => $currency->id]);
        $user->wallets()->attach($wallet);
        $this->fundWallet($wallet, 500);

        $externalWallet = ExternalWallet::factory()->create(['currency_id' => $currency->id]);

        // Creating the pending transaction. 
        // We set user_id to user to match new requirement, though approve/reject might not strictly need it, initiate does.
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'from_wallet_id' => $wallet->id,
            'external_wallet_id' => $externalWallet->id,
            'amount' => 100,
            'transaction_status_id' => TransactionStatus::where('code', 'pending')->first()->id,
            'type' => 'debit'
        ]);

        $this->transferService->approveTransfer($transaction, $manager);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'transaction_status_id' => TransactionStatus::where('code', 'completed')->first()->id
        ]);

        $this->assertEquals(400, $wallet->fresh()->balance);
    }

    /** @test */
    public function it_fails_approve_frozen_wallet()
    {
        $userRole = \App\Domain\User\Models\UserRole::firstOrCreate(['name' => 'user'], ['label' => 'User']);
        $user = User::factory()->create(['role_id' => $userRole->id]);

        $managerRole = \App\Domain\User\Models\UserRole::firstOrCreate(['name' => 'manager'], ['label' => 'Manager']);
        $manager = User::factory()->create(['role_id' => $managerRole->id]);

        $currency = \App\Domain\Currency\Models\Currency::factory()->create(['code' => 'FRZ']);
        $wallet = Wallet::factory()->create([
            'status' => false,
            'currency_id' => $currency->id
        ]);
        $user->wallets()->attach($wallet);
        $this->fundWallet($wallet, 500);

        $externalWallet = ExternalWallet::factory()->create(['currency_id' => $currency->id]);

        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'from_wallet_id' => $wallet->id,
            'external_wallet_id' => $externalWallet->id,
            'amount' => 100,
            'transaction_status_id' => TransactionStatus::where('code', 'pending')->first()->id
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Source wallet is frozen');

        $this->transferService->approveTransfer($transaction, $manager);
    }

    /** @test */
    public function it_rejects_transfer_successfully()
    {
        $userRole = \App\Domain\User\Models\UserRole::firstOrCreate(['name' => 'user'], ['label' => 'User']);
        $user = User::factory()->create(['role_id' => $userRole->id]);

        $managerRole = \App\Domain\User\Models\UserRole::firstOrCreate(['name' => 'manager'], ['label' => 'Manager']);
        $manager = User::factory()->create(['role_id' => $managerRole->id]);

        $currency = \App\Domain\Currency\Models\Currency::factory()->create(['code' => 'REJ']);
        $wallet = Wallet::factory()->create(['currency_id' => $currency->id]);
        $user->wallets()->attach($wallet);
        $this->fundWallet($wallet, 500);

        $externalWallet = ExternalWallet::factory()->create(['currency_id' => $currency->id]);

        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'from_wallet_id' => $wallet->id,
            'external_wallet_id' => $externalWallet->id,
            'amount' => 100,
            'transaction_status_id' => TransactionStatus::where('code', 'pending')->first()->id
        ]);

        $this->transferService->rejectTransfer($transaction, $manager, 'Suspicious activity');

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'transaction_status_id' => TransactionStatus::where('code', 'rejected')->first()->id,
            'rejection_reason' => 'Suspicious activity'
        ]);

        $this->assertEquals(500, $wallet->fresh()->balance);
    }
    /** @test */
    public function it_initiates_internal_transfer_successfully()
    {
        $role = \App\Domain\User\Models\UserRole::firstOrCreate(['name' => 'user'], ['label' => 'User']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $currency = \App\Domain\Currency\Models\Currency::factory()->create();
        $wallet1 = Wallet::factory()->create(['currency_id' => $currency->id]);
        $wallet2 = Wallet::factory()->create(['currency_id' => $currency->id]);

        $user->wallets()->attach($wallet1);
        $this->fundWallet($wallet1, 1000);

        $transaction = $this->transferService->initiateTransfer($wallet1, 'internal', $wallet2->id, 500, $user);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'from_wallet_id' => $wallet1->id,
            'to_wallet_id' => $wallet2->id,
            'amount' => 500,
            'type' => 'debit'
        ]);
    }
}
