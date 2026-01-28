<?php

namespace App\Domain\Currency\Actions;

use App\Domain\Currency\Models\Currency;
use App\Domain\Currency\Services\CurrencyService;

class UpdateCurrencyAction
{
    public function __construct(
        protected CurrencyService $currencyService
    ) {
    }

    public function execute(Currency $currency, array $data): Currency
    {
        return $this->currencyService->update($currency, $data);
    }
}
