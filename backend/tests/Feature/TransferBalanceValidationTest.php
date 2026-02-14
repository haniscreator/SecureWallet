<?php

namespace Tests\Feature;

use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\Currency\Models\Currency;
use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transaction\Models\TransactionStatus;
use App\Domain\Setting\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransferBalanceValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $currency;
    protected $sourceWallet;
    protected $targetWallet;
    protected $completedStatus;
    protected $pendingStatus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->currency = Currency::firstOrCreate(
            ['code' => 'USD'],
            ['name' => 'US Dollar', 'symbol' => '$']
        );

        // Statuses
        $this->completedStatus = TransactionStatus::create(['name' => 'Completed', 'code' => 'completed']);
        $this->pendingStatus = TransactionStatus::create(['name' => 'Pending', 'code' => 'pending']);

        // Create Source Wallet with 1000 Balance
        $this->sourceWallet = Wallet::factory()->create([
            'currency_id' => $this->currency->id,
            'address' => '0xSource123',
            'status' => true
        ]);
        $this->sourceWallet->users()->attach($this->user);

        // Initial Balance 1000
        Transaction::create([
            'to_wallet_id' => $this->sourceWallet->id,
            'type' => 'credit',
            'amount' => 1000,
            'transaction_status_id' => $this->completedStatus->id,
            'reference' => 'Initial'
        ]);

        // Create Target
        $this->targetWallet = Wallet::factory()->create([
            'currency_id' => $this->currency->id,
            'address' => '0xTarget456',
            'status' => true
        ]);
    }

    public function test_available_balance_excludes_pending_debits()
    {
        // 1. Create a PENDING debit of 200
        Transaction::create([
            'from_wallet_id' => $this->sourceWallet->id,
            'type' => 'debit',
            'amount' => 200,
            'transaction_status_id' => $this->pendingStatus->id,
            'reference' => 'Pending Transfer'
        ]);

        // Total Balance: 1000
        // Pending Debit: 200
        // Available: 800

        $response = $this->actingAs($this->user)
            ->getJson('/api/wallets');

        $response->assertStatus(200);

        // Find our wallet in response
        $walletData = collect($response->json('data'))->firstWhere('id', $this->sourceWallet->id);

        $this->assertEquals(1000, $walletData['balance']);
        $this->assertEquals(800, $walletData['available_balance']);
    }

    public function test_cannot_transfer_more_than_available_balance()
    {
        // Limit is 100 to force pending for larger amounts if we were testing limit logic, 
        // but here we just want to test insufficient funds.

        // 1. Create PENDING debit of 500
        Transaction::create([
            'from_wallet_id' => $this->sourceWallet->id,
            'type' => 'debit',
            'amount' => 500,
            'transaction_status_id' => $this->pendingStatus->id,
            'reference' => 'Pending 500'
        ]);

        // Balance 1000, Available 500.
        // Try to transfer 600.

        $response = $this->actingAs($this->user)
            ->postJson('/api/transfers', [
                'source_wallet_id' => $this->sourceWallet->id,
                'type' => 'internal',
                'to_wallet_id' => $this->targetWallet->id,
                'amount' => 600,
                'description' => 'Overdraft attempt'
            ]);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Insufficient funds.']);
    }

    public function test_can_transfer_within_available_balance()
    {
        // 1. Create PENDING debit of 200
        Transaction::create([
            'from_wallet_id' => $this->sourceWallet->id,
            'type' => 'debit',
            'amount' => 200,
            'transaction_status_id' => $this->pendingStatus->id,
            'reference' => 'Pending 200'
        ]);

        // Balance 1000, Available 800.
        // Try to transfer 800.

        $response = $this->actingAs($this->user)
            ->postJson('/api/transfers', [
                'source_wallet_id' => $this->sourceWallet->id,
                'type' => 'internal',
                'to_wallet_id' => $this->targetWallet->id,
                'amount' => 800,
                'description' => 'Full available amount'
            ]);

        $response->assertStatus(201);
    }
}
