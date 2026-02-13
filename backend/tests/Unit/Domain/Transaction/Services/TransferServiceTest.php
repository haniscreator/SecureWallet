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
        Transaction::factory()->create([
            'to_wallet_id' => $wallet->id,
            'amount' => $amount,
            'transaction_status_id' => TransactionStatus::where('code', 'completed')->first()->id,
            'type' => 'credit' // Assuming type is useful for balance calculation filter if checked
        ]);

        // Refresh wallet to ensure relation is loaded if needed, though getBalanceAttribute queries DB
    }

    /** @test */
    public function it_initiates_transfer_successfully()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'currency_id' => 1 // Assuming 1 is USD
        ]);
        $user->wallets()->attach($wallet);
        $this->fundWallet($wallet, 500);

        $externalWallet = ExternalWallet::factory()->create([
            'currency_id' => 1
        ]);

        $transaction = $this->transferService->initiateTransfer($wallet, $externalWallet, 100);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'from_wallet_id' => $wallet->id,
            'external_wallet_id' => $externalWallet->id,
            'amount' => 100,
            'transaction_status_id' => TransactionStatus::where('code', 'pending')->first()->id
        ]);

        // Available balance should be 400
        $this->assertEquals(400, $wallet->fresh()->available_balance);
    }

    /** @test */
    public function it_fails_initiate_transfer_insufficient_funds()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'currency_id' => 1
        ]);
        $user->wallets()->attach($wallet);
        $this->fundWallet($wallet, 50);

        $externalWallet = ExternalWallet::factory()->create([
            'currency_id' => 1
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient funds');

        $this->transferService->initiateTransfer($wallet, $externalWallet, 100);
    }

    /** @test */
    public function it_fails_initiate_transfer_currency_mismatch()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'currency_id' => 1 // USD
        ]);
        $user->wallets()->attach($wallet);
        $this->fundWallet($wallet, 500);

        $externalWallet = ExternalWallet::factory()->create([
            'currency_id' => 2 // EUR
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Currency mismatch');

        $this->transferService->initiateTransfer($wallet, $externalWallet, 100);
    }

    /** @test */
    public function it_approves_transfer_successfully()
    {
        $user = User::factory()->create();
        $manager = User::factory()->create();

        $wallet = Wallet::factory()->create([
            'currency_id' => 1
        ]);
        $user->wallets()->attach($wallet);
        $this->fundWallet($wallet, 500);

        $externalWallet = ExternalWallet::factory()->create(['currency_id' => 1]);

        // Creating the pending transaction
        $transaction = Transaction::factory()->create([
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

        // Balance should be 500 - 100 = 400
        $this->assertEquals(400, $wallet->fresh()->balance);
    }

    /** @test */
    public function it_fails_approve_frozen_wallet()
    {
        $user = User::factory()->create();
        $manager = User::factory()->create();

        $wallet = Wallet::factory()->create([
            'status' => false
        ]);
        $user->wallets()->attach($wallet);
        $this->fundWallet($wallet, 500);

        $externalWallet = ExternalWallet::factory()->create(['currency_id' => 1]);

        $transaction = Transaction::factory()->create([
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
        $user = User::factory()->create();
        $manager = User::factory()->create();

        $wallet = Wallet::factory()->create();
        $user->wallets()->attach($wallet);
        $this->fundWallet($wallet, 500);

        $externalWallet = ExternalWallet::factory()->create(['currency_id' => 1]);

        $transaction = Transaction::factory()->create([
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

        // Balance should be restored (or rather, never deducted, as it was only pending)
        // Available balance should be 500
        $this->assertEquals(500, $wallet->fresh()->balance);
        $this->assertEquals(500, $wallet->fresh()->available_balance);
    }
}
