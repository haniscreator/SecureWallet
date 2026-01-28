<?php

namespace App\Domain\Currency\Services;

use App\Domain\Currency\Models\Currency;
use Illuminate\Database\Eloquent\Collection;

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

    public function create(array $data): Currency
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            return Currency::create([
                'code' => $data['code'],
                'name' => $data['name'],
                'symbol' => $data['symbol'],
                'status' => $data['status'] ?? 1,
            ]);
        });
    }

    public function update(Currency $currency, array $data): Currency
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($currency, $data) {
            $currency->update($data);
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
