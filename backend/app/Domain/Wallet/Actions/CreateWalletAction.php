<?php

namespace App\Domain\Wallet\Actions;

use App\Domain\Wallet\Models\Wallet;
use App\Domain\Wallet\Models\Transaction;
use Illuminate\Support\Facades\DB;

class CreateWalletAction
{
    public function execute(array $data): Wallet
    {
        return DB::transaction(function () use ($data) {
            $wallet = Wallet::create([
                'name' => $data['name'],
                'currency_id' => $data['currency_id'],
                'status' => 1, // 1 = active
            ]);

            // Handle Initial Balance by creating a 'credit' transaction
            if (isset($data['initial_balance']) && $data['initial_balance'] > 0) {
                Transaction::create([
                    'to_wallet_id' => $wallet->id,
                    'type' => 'credit',
                    'amount' => $data['initial_balance'],
                    'reference' => 'Initial Balance',
                ]);
            }

            // Reload to get relationship if needed
            $wallet->load('currency');

            return $wallet;
        });
    }
}
