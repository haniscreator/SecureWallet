<?php

namespace App\Domain\Currency\Actions;

use App\Domain\Currency\Models\Currency;
use App\Domain\Currency\Services\CurrencyService;

class CreateCurrencyAction
{
    public function __construct(
        protected CurrencyService $currencyService
    ) {
    }

    public function execute(\App\Domain\Currency\DataTransferObjects\CurrencyData $data): Currency
    {
        return $this->currencyService->create($data);
    }
}
