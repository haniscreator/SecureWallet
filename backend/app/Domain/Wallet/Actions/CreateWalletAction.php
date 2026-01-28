<?php

namespace App\Domain\Wallet\Actions;

use App\Domain\Wallet\Models\Wallet;
use App\Domain\Wallet\Models\Transaction;
use App\Domain\Currency\Models\Currency;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CreateWalletAction
{
    public function execute(array $data): Wallet
    {
        return DB::transaction(function () use ($data) {
            // Lookup Currency
            $currency = Currency::where('code', $data['currency'])->first();

            if (!$currency) {
                // Should be caught by validation really, but safety check
                throw new InvalidArgumentException("Invalid currency code: {$data['currency']}");
            }

            $wallet = Wallet::create([
                'name' => $data['name'],
                'currency_id' => $currency->id,
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

            // Reload to get relationship if needed
            $wallet->load('currency');

            return $wallet;
        });
    }
}
