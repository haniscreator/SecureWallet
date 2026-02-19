<?php

namespace Tests\Feature;

use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\Currency\Models\Currency;
use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transaction\Models\TransactionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionCancellationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $otherUser;
    protected $currency;
    protected $wallet;
    protected $pendingStatus;
    protected $completedStatus;
    protected $cancelledStatus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();

        $this->currency = Currency::firstOrCreate(
            ['code' => 'USD'],
            ['name' => 'US Dollar', 'symbol' => '$']
        );

        $this->pendingStatus = TransactionStatus::create(['name' => 'Pending', 'code' => 'pending']);
        $this->completedStatus = TransactionStatus::create(['name' => 'Completed', 'code' => 'completed']);
        $this->cancelledStatus = TransactionStatus::create(['name' => 'Cancelled', 'code' => 'cancelled']);

        $this->wallet = Wallet::factory()->create([
            'currency_id' => $this->currency->id,
            'status' => true
        ]);
        $this->wallet->users()->attach($this->user);
    }

    public function test_user_can_cancel_their_own_pending_transfer()
    {
        $transaction = Transaction::create([
            'user_id' => $this->user->id,
            'from_wallet_id' => $this->wallet->id,
            'type' => 'debit',
            'amount' => 500,
            'transaction_status_id' => $this->pendingStatus->id,
            'reference' => 'Test Transaction'
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/transfers/{$transaction->id}/cancel");

        $response->assertStatus(200);
        $this->assertEquals($this->cancelledStatus->id, $transaction->fresh()->transaction_status_id);
    }

    public function test_user_cannot_cancel_others_pending_transfer()
    {
        $transaction = Transaction::create([
            'user_id' => $this->otherUser->id,
            'from_wallet_id' => $this->wallet->id,
            'type' => 'debit',
            'amount' => 500,
            'transaction_status_id' => $this->pendingStatus->id,
            'reference' => 'Other User Transaction'
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/transfers/{$transaction->id}/cancel");

        $response->assertStatus(400);
        $response->assertJson(['message' => 'You can only cancel your own transactions.']);
        $this->assertEquals($this->pendingStatus->id, $transaction->fresh()->transaction_status_id);
    }

    public function test_user_cannot_cancel_already_completed_transfer()
    {
        $transaction = Transaction::create([
            'user_id' => $this->user->id,
            'from_wallet_id' => $this->wallet->id,
            'type' => 'debit',
            'amount' => 500,
            'transaction_status_id' => $this->completedStatus->id,
            'reference' => 'Completed Transaction'
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/transfers/{$transaction->id}/cancel");

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Only pending transactions can be cancelled.']);
        $this->assertEquals($this->completedStatus->id, $transaction->fresh()->transaction_status_id);
    }
}
