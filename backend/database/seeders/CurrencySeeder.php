<?php

namespace Database\Seeders;

use App\Domain\Currency\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['code' => 'USD', 'symbol' => '$', 'name' => 'US Dollar', 'status' => true],
            ['code' => 'EUR', 'symbol' => '€', 'name' => 'Euro', 'status' => true],
        ];

        foreach ($currencies as $currency) {
            Currency::firstOrCreate(['code' => $currency['code']], $currency);
        }
    }
}
