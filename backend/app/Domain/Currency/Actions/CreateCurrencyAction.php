<?php

namespace App\Domain\Currency\Actions;

use App\Domain\Currency\Models\Currency;
use App\Domain\Currency\Services\CurrencyService;
use App\Domain\Currency\DataTransferObjects\CurrencyData;

class CreateCurrencyAction
{
    public function __construct(
        protected CurrencyService $currencyService
    ) {
    }

    public function execute(CurrencyData $data): Currency
    {
        return $this->currencyService->create($data);
    }
}
