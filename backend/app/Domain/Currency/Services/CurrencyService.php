<?php

namespace App\Domain\Currency\Services;

use App\Domain\Currency\Models\Currency;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\Currency\DataTransferObjects\CurrencyData;

class CurrencyService
{
    public function listCurrencies(): Collection
    {
        return Currency::all();
    }

    public function getCurrency(int $id): Currency
    {
        return Currency::findOrFail($id);
    }

    public function create(CurrencyData $data): Currency
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            return Currency::create(array_merge(
                ['status' => 1],
                $data->toArray()
            ));
        });
    }

    public function update(Currency $currency, CurrencyData $data): Currency
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($currency, $data) {
            $currency->update($data->toArray());
            return $currency;
        });
    }

    public function delete(Currency $currency): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($currency) {
            $currency->delete();
        });
    }
}
