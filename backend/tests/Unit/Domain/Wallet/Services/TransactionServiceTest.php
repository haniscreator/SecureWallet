<?php

namespace Tests\Unit\Domain\Wallet\Services;

use Tests\TestCase;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\Currency\Models\Currency;
use App\Domain\Wallet\Models\Transaction;
use App\Domain\Wallet\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TransactionService $transactionService;
    protected Wallet $wallet;
    protected Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transactionService = new TransactionService();

        $this->currency = Currency::create([
            'code' => 'TEST-' . uniqid(),
            'name' => 'Test Currency',
            'symbol' => '$',
        ]);

        $this->wallet = Wallet::create([
            'name' => 'Test Wallet',
            'currency_id' => $this->currency->id,
        ]);
    }

    public function test_list_transactions_returns_incoming_and_outgoing()
    {
        $otherWallet = Wallet::create([
            'name' => 'Other Wallet',
            'currency_id' => $this->currency->id,
        ]);

        // Incoming (Credit)
        Transaction::create([
            'to_wallet_id' => $this->wallet->id,
            'amount' => 100,
            'type' => 'credit',
            'reference' => 'Incoming',
        ]);

        // Outgoing (Debit) - simulating a transfer out
        Transaction::create([
            'from_wallet_id' => $this->wallet->id,
            'to_wallet_id' => $otherWallet->id,
            'amount' => 50,
            'type' => 'debit',
            'reference' => 'Outgoing',
        ]);

        // Unrelated transaction
        Transaction::create([
            'to_wallet_id' => $otherWallet->id,
            'amount' => 200,
            'type' => 'credit',
        ]);

        $transactions = $this->transactionService->listTransactions($this->wallet);

        $this->assertEquals(2, $transactions->count());
        $this->assertTrue($transactions->contains('reference', 'Incoming'));
        $this->assertTrue($transactions->contains('reference', 'Outgoing'));
    }

    public function test_filter_transactions_by_type()
    {
        Transaction::create([
            'to_wallet_id' => $this->wallet->id,
            'amount' => 100,
            'type' => 'credit',
        ]);

        Transaction::create([
            'from_wallet_id' => $this->wallet->id,
            'amount' => 50,
            'type' => 'debit',
        ]);

        $credits = $this->transactionService->listTransactions($this->wallet, ['type' => 'credit']);
        $this->assertEquals(1, $credits->count());
        $this->assertEquals('credit', $credits->first()->type);

        $debits = $this->transactionService->listTransactions($this->wallet, ['type' => 'debit']);
        $this->assertEquals(1, $debits->count());
        $this->assertEquals('debit', $debits->first()->type);
    }

    public function test_filter_transactions_by_date_range()
    {
        // Old transaction
        Transaction::create([
            'to_wallet_id' => $this->wallet->id,
            'amount' => 100,
            'type' => 'credit',
            'created_at' => Carbon::now()->subDays(10),
        ]);

        // Recent transaction
        Transaction::create([
            'to_wallet_id' => $this->wallet->id,
            'amount' => 200,
            'type' => 'credit',
            'created_at' => Carbon::now(),
        ]);

        // Filter for last 5 days
        $recent = $this->transactionService->listTransactions($this->wallet, [
            'from_date' => Carbon::now()->subDays(5)->toDateTimeString()
        ]);

        $this->assertEquals(1, $recent->count());
        $this->assertEquals(200, $recent->first()->amount);
    }
}
