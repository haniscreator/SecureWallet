<?php

namespace App\Domain\Currency\Actions;

use App\Domain\Currency\Services\CurrencyService;
use Illuminate\Database\Eloquent\Collection;

class ListCurrenciesAction
{
    public function __construct(
        protected CurrencyService $currencyService
    ) {
    }

    public function execute(): Collection
    {
        return $this->currencyService->listCurrencies();
    }
}
