<?php

namespace App\Domain\Currency\Actions;

use App\Domain\Currency\Models\Currency;
use App\Domain\Currency\Services\CurrencyService;
use App\Domain\Currency\DataTransferObjects\CurrencyData;

class UpdateCurrencyAction
{
    public function __construct(
        protected CurrencyService $currencyService
    ) {
    }

    public function execute(Currency $currency, CurrencyData $data): Currency
    {
        return $this->currencyService->update($currency, $data);
    }
}
