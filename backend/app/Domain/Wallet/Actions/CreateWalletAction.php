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
                'currency' => $data['currency'],
                'status' => 'active',
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

            return $wallet;
        });
    }
}
