<?php

namespace App\Domain\Currency\Actions;

use App\Domain\Currency\Models\Currency;
use App\Domain\Currency\Services\CurrencyService;

class DeleteCurrencyAction
{
    public function __construct(
        protected CurrencyService $currencyService
    ) {
    }

    public function execute(Currency $currency): void
    {
        $this->currencyService->delete($currency);
    }
}
