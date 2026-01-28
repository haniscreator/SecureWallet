<?php

namespace Tests\Unit\Domain\Wallet\Services;

use Tests\TestCase;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\Currency\Models\Currency;
use App\Domain\Wallet\Models\Transaction;
use App\Domain\Wallet\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WalletServiceTest extends TestCase
{
    use RefreshDatabase;

    protected WalletService $walletService;
    protected Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();
        $this->walletService = new WalletService();

        // Use unique code to prevent collision if DB doesn't refresh perfectly
        $this->currency = Currency::create([
            'code' => 'TEST-' . uniqid(),
            'name' => 'Test Currency',
            'symbol' => '$',
        ]);
    }

    public function test_create_wallet_successfully()
    {
        $data = new \App\Domain\Wallet\DataTransferObjects\WalletData(
            name: 'My Wallet',
            currency_id: $this->currency->id,
            initial_balance: 0
        );

        $wallet = $this->walletService->create($data);

        $this->assertInstanceOf(Wallet::class, $wallet);
        $this->assertEquals('My Wallet', $wallet->name);
        $this->assertEquals($this->currency->id, $wallet->currency_id);
    }

    public function test_create_wallet_with_initial_balance_creates_transaction()
    {
        $data = new \App\Domain\Wallet\DataTransferObjects\WalletData(
            name: 'Funded Wallet',
            currency_id: $this->currency->id,
            initial_balance: 1000
        );

        $wallet = $this->walletService->create($data);

        $this->assertDatabaseHas('wallets', ['id' => $wallet->id]);

        // Check transaction
        $this->assertDatabaseHas('transactions', [
            'to_wallet_id' => $wallet->id,
            'type' => 'credit',
            'amount' => 1000,
            'reference' => 'Initial Balance',
        ]);

        // Check computed balance if applicable (assuming attribute accessor works if relation loaded)
        // Or manually check transactions sum
        $this->assertEquals(1000, $wallet->incomingTransactions()->sum('amount')); // simple sum for credit only
    }

    public function test_update_wallet()
    {
        $wallet = Wallet::create([
            'name' => 'Old Name',
            'currency_id' => $this->currency->id,
            'status' => true,
        ]);

        $updatedWallet = $this->walletService->update($wallet, new \App\Domain\Wallet\DataTransferObjects\WalletData(
            name: 'New Name',
            currency_id: $this->currency->id // Required by DTO constructor
        ));

        $this->assertEquals('New Name', $updatedWallet->name);
        $this->assertDatabaseHas('wallets', ['id' => $wallet->id, 'name' => 'New Name']);
    }

    public function test_update_wallet_partial()
    {
        $wallet = Wallet::create([
            'name' => 'Original Name',
            'currency_id' => $this->currency->id,
            'status' => true,
        ]);

        // Only update name
        $data = new \App\Domain\Wallet\DataTransferObjects\WalletData(
            name: 'Updated Name Only'
        );

        $updatedWallet = $this->walletService->update($wallet, $data);

        $this->assertEquals('Updated Name Only', $updatedWallet->name);
        $this->assertEquals($this->currency->id, $updatedWallet->currency_id); // Should remain unchanged
    }

    public function test_update_wallet_status_freeze()
    {
        $wallet = Wallet::create([
            'name' => 'Active Wallet',
            'currency_id' => $this->currency->id,
            'status' => true,
        ]);

        $this->walletService->updateStatus($wallet, false);

        $this->assertDatabaseHas('wallets', ['id' => $wallet->id, 'status' => 0]); // 0 for false
    }

    public function test_assign_users_to_wallet()
    {
        $wallet = Wallet::create([
            'name' => 'Shared Wallet',
            'currency_id' => $this->currency->id,
        ]);

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->walletService->assignUsers($wallet, [$user1->id, $user2->id]);

        $this->assertTrue($wallet->users->contains($user1));
        $this->assertTrue($wallet->users->contains($user2));
    }

    public function test_list_wallets_as_admin_returns_all()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Wallet::create(['name' => 'W1', 'currency_id' => $this->currency->id]);
        Wallet::create(['name' => 'W2', 'currency_id' => $this->currency->id]);

        $wallets = $this->walletService->listWallets($admin);

        $this->assertGreaterThanOrEqual(2, $wallets->count());
    }

    public function test_list_wallets_as_user_returns_only_assigned()
    {
        $user = User::factory()->create(['role' => 'user']);

        $w1 = Wallet::create(['name' => 'Assigned', 'currency_id' => $this->currency->id]);
        $w2 = Wallet::create(['name' => 'Not Assigned', 'currency_id' => $this->currency->id]);

        $w1->users()->attach($user->id);

        $wallets = $this->walletService->listWallets($user);

        $this->assertEquals(1, $wallets->count());
        $this->assertEquals('Assigned', $wallets->first()->name);
    }
}
