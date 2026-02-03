<?php

namespace Tests\Unit\Domain\Transaction\Services;

use Tests\TestCase;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\Currency\Models\Currency;
use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transaction\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TransactionService $transactionService;
    protected Currency $currency1;
    protected Currency $currency2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transactionService = new TransactionService();

        $this->currency1 = Currency::firstOrCreate(['code' => 'USD'], ['name' => 'US Dollar', 'symbol' => '$']);
        $this->currency2 = Currency::firstOrCreate(['code' => 'EUR'], ['name' => 'Euro', 'symbol' => '€']);
    }

    public function test_get_aggregated_balances()
    {
        $user = User::factory()->create();

        $w1 = Wallet::factory()->create(['currency_id' => $this->currency1->id]); // USD
        $w2 = Wallet::factory()->create(['currency_id' => $this->currency1->id]); // USD
        $w3 = Wallet::factory()->create(['currency_id' => $this->currency2->id]); // EUR

        $user->wallets()->attach([$w1->id, $w2->id, $w3->id]);

        // W1 (USD): +100
        Transaction::create(['to_wallet_id' => $w1->id, 'type' => 'credit', 'amount' => 100]);

        // W2 (USD): +50, -20 = 30
        Transaction::create(['to_wallet_id' => $w2->id, 'type' => 'credit', 'amount' => 50]);
        Transaction::create(['from_wallet_id' => $w2->id, 'type' => 'debit', 'amount' => 20]);

        // W3 (EUR): +200
        Transaction::create(['to_wallet_id' => $w3->id, 'type' => 'credit', 'amount' => 200]);

        $totals = $this->transactionService->getAggregatedBalances($user);

        // Expected USD: 100 + 30 = 130
        // Expected EUR: 200

        $this->assertEquals(130, $totals['USD']['amount']);
        $this->assertEquals('$', $totals['USD']['symbol']);

        $this->assertEquals(200, $totals['EUR']['amount']);
        $this->assertEquals('€', $totals['EUR']['symbol']);
    }
}
