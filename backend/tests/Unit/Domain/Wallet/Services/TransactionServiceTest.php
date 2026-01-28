<?php

namespace Tests\Unit\Domain\Wallet\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domain\Wallet\Services\TransactionService;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\Wallet\Models\Transaction;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_transactions_filters()
    {
        $wallet = Wallet::create(['name' => 'Test Wallet', 'currency_id' => 1, 'status' => 1]);

        // Create transactions with fixed dates
        Transaction::create([
            'to_wallet_id' => $wallet->id,
            'type' => 'credit',
            'amount' => 100,
            'reference' => 'Ref 1',
            'created_at' => '2024-01-01 12:00:00'
        ]);

        Transaction::create([
            'from_wallet_id' => $wallet->id,
            'type' => 'debit',
            'amount' => 50,
            'reference' => 'Ref 2',
            'created_at' => '2024-01-05 12:00:00'
        ]);

        $service = new TransactionService();

        // Test Type Filter
        $credits = $service->listTransactions($wallet, ['type' => 'credit']);
        $this->assertCount(1, $credits);
        $this->assertEquals('credit', $credits->first()->type);

        // Test Date Filter
        // Filter from 2024-01-03, should only get the 2024-01-05 transaction
        $recent = $service->listTransactions($wallet, ['from_date' => '2024-01-03']);
        $this->assertCount(1, $recent);
        $this->assertEquals('debit', $recent->first()->type);
    }
}
