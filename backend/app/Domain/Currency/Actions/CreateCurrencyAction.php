<?php

namespace App\Domain\Currency\Actions;

use App\Domain\Currency\Models\Currency;
use Illuminate\Support\Facades\DB;

class CreateCurrencyAction
{
    public function execute(array $data): Currency
    {
        return DB::transaction(function () use ($data) {
            return Currency::create([
                'code' => $data['code'],
                'name' => $data['name'],
                'symbol' => $data['symbol'],
                'status' => $data['status'] ?? 1, // Default to active
            ]);
        });
    }
}
