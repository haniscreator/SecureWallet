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
        $wallets = Wallet::all();
        if ($wallets->count() < 2)
            return;

        $user = User::first(); // Admin

        // 0. Initial Deposits (Ensure positive balance)
        // 0. Initial Deposits (Ensure positive balance)
        foreach ($wallets as $wallet) {
            $amount = $wallet->name === 'Wallet-2' ? 30000.00 : 50000.00;

            Transaction::create([
                'to_wallet_id' => $wallet->id,
                'transaction_status_id' => $completed->id,
                'type' => 'credit',
                'amount' => $amount,
                'reference' => 'Initial Seed Deposit',
                'created_at' => now()->subYear(),
            ]);
        }


        // 1. Internal Transfer (Completed, Small Amount)
        // 1. Internal Transfer (Completed, Small Amount) - REMOVED
        // 2. Internal Transfer (Completed, Large Amount - Approved) - REMOVED
        // 3. External Transfer (Pending, Large Amount) - REMOVED
        // 4. Internal Transfer (Rejected) - REMOVED
        // 5. Incoming Credit (Completed) - REMOVED
    }
}

