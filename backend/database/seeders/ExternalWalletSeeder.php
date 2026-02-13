<?php

namespace Database\Seeders;

use App\Domain\Wallet\Models\ExternalWallet;
use App\Domain\Currency\Models\Currency;
use Illuminate\Database\Seeder;

class ExternalWalletSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure currencies exist
        $usd = Currency::where('code', 'USD')->first();
        $eur = Currency::where('code', 'EUR')->first();

        if (!$usd || !$eur) {
            return;
        }

        $wallets = [
            [
                'address' => '0x71C7656EC7ab88b098defB751B7401B5f6d8976F',
                'name' => 'Binance Hot Wallet',
                'currency_id' => $usd->id,
                'status' => true,
            ],
            [
                'address' => '0x3f5CE5FBFe3E9af3971dD833D26bA9b5C936f0bE',
                'name' => 'Coinbase',
                'currency_id' => $usd->id,
                'status' => true,
            ],
            [
                'address' => '0x250e76987d838a75310c34bf422ea9f1AC408acc',
                'name' => 'Kraken',
                'currency_id' => $eur->id,
                'status' => true,
            ],
            [
                'address' => '0x123abc12345def67890ghi12345jkl67890mno',
                'name' => 'External Vendor',
                'currency_id' => $usd->id,
                'status' => true,
            ],
            [
                'address' => '0xdef456partnerwallet1234567890abcdef123',
                'name' => 'Partner Wallet',
                'currency_id' => $eur->id,
                'status' => false, // Inactive
            ],
        ];


        foreach ($wallets as $wallet) {
            ExternalWallet::create($wallet);
        }
    }
}
