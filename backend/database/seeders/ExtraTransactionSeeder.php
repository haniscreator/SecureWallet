<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\Transaction\Models\Transaction;
use Illuminate\Support\Carbon;

class ExtraTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wallets = Wallet::all();
        $references = [
            'Marketing Budget',
            'Salary Payment',
            'Server Infrastructure',
            'Consulting Fees',
            'Office Supplies',
            'SaaS Subscription',
            'Travel Expenses',
            'Client Payment - Invoice #1023',
            'Client Payment - Invoice #1024'
        ];

        foreach ($wallets as $wallet) {
            // Generate EXACTLY 3 transactions per wallet as requested
            for ($i = 0; $i < 3; $i++) {
                $isCredit = rand(0, 1) === 1; // 50/50 chance
                $amount = rand(100, 5000);
                $reference = $references[array_rand($references)];
                $date = Carbon::now()->subDays(rand(1, 30));

                if ($isCredit) {
                    // Credit: External money coming in (from null to wallet)
                    Transaction::create([
                        'from_wallet_id' => null,
                        'to_wallet_id' => $wallet->id,
                        'type' => 'credit',
                        'amount' => $amount,
                        'reference' => $reference,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);
                } else {
                    // Debit: Expense (from wallet to null)
                    // Ensure wallet has enough balance or allow negative if not checked strictly
                    // For dummy data, we just create it.

                    Transaction::create([
                        'from_wallet_id' => $wallet->id,
                        'to_wallet_id' => null,
                        'type' => 'debit',
                        'amount' => $amount,
                        'reference' => $reference,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);
                }
            }
        }
    }
}
