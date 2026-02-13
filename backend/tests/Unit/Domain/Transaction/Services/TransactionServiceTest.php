<?php

namespace Tests\Unit\Domain\Transaction\Services;

use Tests\TestCase;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\Currency\Models\Currency;
use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transaction\Models\TransactionStatus;
use App\Domain\Transaction\Services\TransactionService;
use App\Domain\Transaction\DataTransferObjects\TransactionFilterData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TransactionService $transactionService;
    protected Currency $currency1;
    protected Currency $currency2;
    protected TransactionStatus $completedStatus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transactionService = new TransactionService();

        $this->currency1 = Currency::firstOrCreate(['code' => 'USD'], ['name' => 'US Dollar', 'symbol' => '$']);
        $this->currency2 = Currency::firstOrCreate(['code' => 'EUR'], ['name' => 'Euro', 'symbol' => '€']);

        $this->completedStatus = TransactionStatus::create([
            'name' => 'Completed',
            'code' => 'completed',
        ]);
    }

    public function test_get_aggregated_balances()
    {
        $user = User::factory()->create();

        $w1 = Wallet::factory()->create(['currency_id' => $this->currency1->id]); // USD
        $w2 = Wallet::factory()->create(['currency_id' => $this->currency1->id]); // USD
        $w3 = Wallet::factory()->create(['currency_id' => $this->currency2->id]); // EUR

        $user->wallets()->attach([$w1->id, $w2->id, $w3->id]);

        // W1 (USD): +100
        Transaction::create([
            'to_wallet_id' => $w1->id,
            'type' => 'credit',
            'amount' => 100,
            'transaction_status_id' => $this->completedStatus->id
        ]);

        // W2 (USD): +50, -20 = 30
        Transaction::create([
            'to_wallet_id' => $w2->id,
            'type' => 'credit',
            'amount' => 50,
            'transaction_status_id' => $this->completedStatus->id
        ]);
        Transaction::create([
            'from_wallet_id' => $w2->id,
            'type' => 'debit',
            'amount' => 20,
            'transaction_status_id' => $this->completedStatus->id
        ]);

        // W3 (EUR): +200
        Transaction::create([
            'to_wallet_id' => $w3->id,
            'type' => 'credit',
            'amount' => 200,
            'transaction_status_id' => $this->completedStatus->id
        ]);

        $totals = $this->transactionService->getAggregatedBalances($user);

        // Expected USD: 100 + 30 = 130
        // Expected EUR: 200

        $this->assertEquals(130, $totals['USD']['amount']);
        $this->assertEquals('$', $totals['USD']['symbol']);

        $this->assertEquals(200, $totals['EUR']['amount']);
        $this->assertEquals('€', $totals['EUR']['symbol']);
    }

    public function test_list_transactions_returns_incoming_and_outgoing()
    {
        $wallet = Wallet::factory()->create(['currency_id' => $this->currency1->id]);
        $otherWallet = Wallet::factory()->create(['currency_id' => $this->currency1->id]);

        // Incoming (Credit)
        Transaction::create([
            'to_wallet_id' => $wallet->id,
            'amount' => 100,
            'type' => 'credit',
            'reference' => 'Incoming',
            'transaction_status_id' => $this->completedStatus->id
        ]);

        // Outgoing (Debit) - simulating a transfer out
        Transaction::create([
            'from_wallet_id' => $wallet->id,
            'to_wallet_id' => $otherWallet->id,
            'amount' => 50,
            'type' => 'debit',
            'reference' => 'Outgoing',
            'transaction_status_id' => $this->completedStatus->id
        ]);

        // Unrelated transaction
        Transaction::create([
            'to_wallet_id' => $otherWallet->id,
            'amount' => 200,
            'type' => 'credit',
            'transaction_status_id' => $this->completedStatus->id
        ]);

        $transactions = $this->transactionService->listTransactions(
            $wallet,
            new TransactionFilterData(
                type: null,
                from_date: null,
                to_date: null,
                reference: null
            )
        );

        $this->assertEquals(2, $transactions->count());
        $this->assertTrue($transactions->contains('reference', 'Incoming'));
        $this->assertTrue($transactions->contains('reference', 'Outgoing'));
    }

    public function test_filter_transactions_by_type()
    {
        $wallet = Wallet::factory()->create(['currency_id' => $this->currency1->id]);

        Transaction::create([
            'to_wallet_id' => $wallet->id,
            'amount' => 100,
            'type' => 'credit',
            'transaction_status_id' => $this->completedStatus->id
        ]);

        Transaction::create([
            'from_wallet_id' => $wallet->id,
            'amount' => 50,
            'type' => 'debit',
            'transaction_status_id' => $this->completedStatus->id
        ]);

        $credits = $this->transactionService->listTransactions(
            $wallet,
            new TransactionFilterData(
                type: 'credit',
                from_date: null,
                to_date: null,
                reference: null
            )
        );
        $this->assertEquals(1, $credits->count());
        $this->assertEquals('credit', $credits->first()->type);

        $debits = $this->transactionService->listTransactions(
            $wallet,
            new TransactionFilterData(
                type: 'debit',
                from_date: null,
                to_date: null,
                reference: null
            )
        );
        $this->assertEquals(1, $debits->count());
        $this->assertEquals('debit', $debits->first()->type);
    }

    public function test_filter_transactions_by_date_range()
    {
        $wallet = Wallet::factory()->create(['currency_id' => $this->currency1->id]);

        // Old transaction
        Transaction::create([
            'to_wallet_id' => $wallet->id,
            'amount' => 100,
            'type' => 'credit',
            'created_at' => Carbon::now()->subDays(10),
            'transaction_status_id' => $this->completedStatus->id
        ]);

        // Recent transaction
        Transaction::create([
            'to_wallet_id' => $wallet->id,
            'amount' => 200,
            'type' => 'credit',
            'created_at' => Carbon::now(),
            'transaction_status_id' => $this->completedStatus->id
        ]);

        // Filter for last 5 days
        $recent = $this->transactionService->listTransactions(
            $wallet,
            new TransactionFilterData(
                type: null,
                from_date: Carbon::now()->subDays(5)->toDateTimeString(),
                to_date: null,
                reference: null
            )
        );

        $this->assertEquals(1, $recent->count());
        $this->assertEquals(200, $recent->first()->amount);
    }
}
