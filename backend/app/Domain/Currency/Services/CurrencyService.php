<?php

namespace App\Domain\Currency\Services;

use App\Domain\Currency\Models\Currency;
use Illuminate\Database\Eloquent\Collection;

class CurrencyService
{
    public function __construct(
        protected \App\Domain\Currency\Actions\CreateCurrencyAction $createCurrencyAction,
        protected \App\Domain\Currency\Actions\UpdateCurrencyAction $updateCurrencyAction,
        protected \App\Domain\Currency\Actions\DeleteCurrencyAction $deleteCurrencyAction
    ) {
    }

    public function listCurrencies(): Collection
    {
        return Currency::all();
    }

    public function getCurrency(int $id): Currency
    {
        return Currency::findOrFail($id);
    }

    public function createCurrency(array $data): Currency
    {
        return $this->createCurrencyAction->execute($data);
    }

    public function updateCurrency(Currency $currency, array $data): Currency
    {
        return $this->updateCurrencyAction->execute($currency, $data);
    }

    public function deleteCurrency(Currency $currency): void
    {
        $this->deleteCurrencyAction->execute($currency);
    }
}
