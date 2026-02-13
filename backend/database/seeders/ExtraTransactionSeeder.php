<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\Transaction\Models\Transaction;
use Illuminate\Support\Carbon;

use App\Domain\Transaction\Models\TransactionStatus;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\ExternalWallet;

class ExtraTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate existing transactions
        Transaction::truncate();

        // Get Statuses
        $completed = TransactionStatus::where('code', 'completed')->first();
        $pending = TransactionStatus::where('code', 'pending')->first();
        $rejected = TransactionStatus::where('code', 'rejected')->first();

        // Get Wallets
        $wallets = Wallet::take(2)->get();
        if ($wallets->count() < 2)
            return;

        $user = User::first(); // Admin

        // 0. Initial Deposits (Ensure positive balance)
        foreach ($wallets as $wallet) {
            Transaction::create([
                'to_wallet_id' => $wallet->id,
                'transaction_status_id' => $completed->id,
                'type' => 'credit',
                'amount' => 50000.00, // Large Enough
                'reference' => 'Initial Seed Deposit',
                'created_at' => now()->subYear(),
            ]);
        }


        // 1. Internal Transfer (Completed, Small Amount)
        Transaction::create([
            'from_wallet_id' => $wallets[0]->id,
            'to_wallet_id' => $wallets[1]->id,
            'transaction_status_id' => $completed->id,
            'type' => 'debit',
            'amount' => 500.00,
            'reference' => 'Small Internal Transfer',
            'created_at' => now()->subDays(2),
        ]);

        // 2. Internal Transfer (Completed, Large Amount - Approved)
        Transaction::create([
            'from_wallet_id' => $wallets[1]->id,
            'to_wallet_id' => $wallets[0]->id,
            'transaction_status_id' => $completed->id,
            'type' => 'debit',
            'amount' => 1500.00,
            'reference' => 'Large Internal Transfer',
            'approved_by' => $user->id,
            'approved_at' => now()->subDay(),
            'created_at' => now()->subDays(2),
        ]);

        // 3. External Transfer (Pending, Large Amount)
        // Need external wallet
        $ext = ExternalWallet::first();
        if ($ext) {
            Transaction::create([
                'from_wallet_id' => $wallets[0]->id,
                'external_wallet_id' => $ext->id,
                'transaction_status_id' => $pending->id,
                'type' => 'debit',
                'amount' => 2000.00,
                'reference' => 'Payment to Vendor',
                'created_at' => now()->subHour(),
            ]);
        }

        // 4. Internal Transfer (Rejected)
        Transaction::create([
            'from_wallet_id' => $wallets[0]->id,
            'to_wallet_id' => $wallets[1]->id,
            'transaction_status_id' => $rejected->id,
            'type' => 'debit',
            'amount' => 5000.00,
            'reference' => 'Suspicious Transfer',
            'rejection_reason' => 'Suspicious activity detected',
            'created_at' => now()->subDays(3),
        ]);

        // 5. Incoming Credit (Completed)
        Transaction::create([
            'to_wallet_id' => $wallets[0]->id,
            'transaction_status_id' => $completed->id,
            'type' => 'credit',
            'amount' => 10000.00,
            'reference' => 'Deposit',
            'created_at' => now()->subMonth(),
        ]);
    }
}

