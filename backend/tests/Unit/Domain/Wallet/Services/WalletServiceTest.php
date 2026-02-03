<?php

namespace Tests\Unit\Domain\Wallet\Services;

use Tests\TestCase;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\Currency\Models\Currency;
use App\Domain\Transaction\Models\Transaction;
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
    public function test_get_wallets_for_dashboard()
    {
        $user = User::factory()->create();

        // Create 4 wallets with different timestamps
        $w1 = Wallet::factory()->create(['created_at' => now(), 'name' => 'W1', 'currency_id' => $this->currency->id]);
        $w2 = Wallet::factory()->create(['created_at' => now()->subHour(), 'name' => 'W2', 'currency_id' => $this->currency->id]);
        $w3 = Wallet::factory()->create(['created_at' => now()->subHours(2), 'name' => 'W3', 'currency_id' => $this->currency->id]);
        $w4 = Wallet::factory()->create(['created_at' => now()->subHours(3), 'name' => 'W4', 'currency_id' => $this->currency->id]);

        $user->wallets()->attach([$w1->id, $w2->id, $w3->id, $w4->id]);

        // Add transactions
        // W1: +1000 (credit)
        Transaction::create(['to_wallet_id' => $w1->id, 'type' => 'credit', 'amount' => 1000]);

        // W2: +500 (credit), -200 (debit as outgoing)
        // Wait, frontend logic: 
        // if credit -> +
        // if debit -> -
        // It DOES NOT check if it is incoming or outgoing? 
        // The frontend iterates `walletApi.getTransactions(wallet.id)`.
        // `TransactionService::listTransactions` returns BOTH incoming and outgoing.
        // So if I have an outgoing transaction of type 'debit', it is present in the list.
        // Logic: if debit -> subtract. Correct.
        // What if I have an incoming transaction of type 'debit'? (Reversal?) -> subtract. Correct.
        // What if I have an outgoing transaction of type 'credit'? (refund to me?) -> add. Correct.

        Transaction::create(['to_wallet_id' => $w2->id, 'type' => 'credit', 'amount' => 500]);
        Transaction::create(['from_wallet_id' => $w2->id, 'type' => 'debit', 'amount' => 200]);

        // W3: Complex case. 
        // Incoming Debit (e.g. chargeback): -100
        // Outgoing Credit (e.g. correction?): +50
        Transaction::create(['to_wallet_id' => $w3->id, 'type' => 'debit', 'amount' => 100]);
        Transaction::create(['from_wallet_id' => $w3->id, 'type' => 'credit', 'amount' => 50]);

        $results = $this->walletService->getWalletsForDashboard($user);

        // Sorting check (latest created first) -> W1, W2, W3 (W4 excluded as limit 3)
        // W1 created now, W2 -1h, W3 -2h, W4 -3h
        $this->assertCount(3, $results);
        $this->assertEquals('W1', $results[0]->name);
        $this->assertEquals('W2', $results[1]->name);
        $this->assertEquals('W3', $results[2]->name);

        // Balance check (dashboard_balance)
        // Balance check (dashboard_balance)
        $this->assertEquals(1000, $results[0]->dashboard_balance);
        $this->assertEquals(300, $results[1]->dashboard_balance); // 500 - 200
        $this->assertEquals(-50, $results[2]->dashboard_balance); // -100 + 50

        // Users count check
        $this->assertEquals(1, $results[0]->users_count); // Attached in setup
    }
}
