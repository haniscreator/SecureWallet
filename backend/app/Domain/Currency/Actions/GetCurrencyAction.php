<?php

namespace App\Domain\Currency\Actions;

use App\Domain\Currency\Models\Currency;
use App\Domain\Currency\Services\CurrencyService;

class GetCurrencyAction
{
    public function __construct(
        protected CurrencyService $currencyService
    ) {
    }

    public function execute(int $id): Currency
    {
        return $this->currencyService->getCurrency($id);
    }
}
