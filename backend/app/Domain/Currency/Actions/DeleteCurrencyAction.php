<?php

namespace App\Domain\Currency\Actions;

use App\Domain\Currency\Models\Currency;
use Illuminate\Support\Facades\DB;

class DeleteCurrencyAction
{
    public function execute(Currency $currency): void
    {
        DB::transaction(function () use ($currency) {
            $currency->delete();
        });
    }
}
