<?php

namespace App\Domain\Currency\Actions;

use App\Domain\Currency\Models\Currency;
use Illuminate\Support\Facades\DB;

class UpdateCurrencyAction
{
    public function execute(Currency $currency, array $data): Currency
    {
        return DB::transaction(function () use ($currency, $data) {
            $currency->update($data);
            return $currency;
        });
    }
}
