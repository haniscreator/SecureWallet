<?php

namespace App\Http\Controllers\Api\V1\Currency;

use App\Http\Controllers\Controller;
use App\Domain\Currency\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    protected $currencyService;
    protected $createCurrencyAction;
    protected $updateCurrencyAction;
    protected $deleteCurrencyAction;

    public function __construct(
        \App\Domain\Currency\Services\CurrencyService $currencyService,
        \App\Domain\Currency\Actions\CreateCurrencyAction $createCurrencyAction,
        \App\Domain\Currency\Actions\UpdateCurrencyAction $updateCurrencyAction,
        \App\Domain\Currency\Actions\DeleteCurrencyAction $deleteCurrencyAction
    ) {
        $this->currencyService = $currencyService;
        $this->createCurrencyAction = $createCurrencyAction;
        $this->updateCurrencyAction = $updateCurrencyAction;
        $this->deleteCurrencyAction = $deleteCurrencyAction;
    }

    public function index(Request $request)
    {
        return response()->json($this->currencyService->listCurrencies());
    }

    public function show(Request $request, $id)
    {
        return response()->json($this->currencyService->getCurrency($id));
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'code' => 'required|string|unique:currencies,code|max:10',
            'name' => 'required|string|max:255',
            'symbol' => 'nullable|string|max:10',
            'status' => 'sometimes|boolean',
        ]);

        $currency = $this->createCurrencyAction->execute($validated);

        return response()->json(['message' => 'Currency created', 'currency' => $currency], 201);
    }

    public function update(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $currency = $this->currencyService->getCurrency($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'symbol' => 'nullable|string|max:10',
            'status' => 'sometimes|boolean',
        ]);

        $updatedCurrency = $this->updateCurrencyAction->execute($currency, $validated);

        return response()->json(['message' => 'Currency updated', 'currency' => $updatedCurrency]);
    }

    public function destroy(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $currency = $this->currencyService->getCurrency($id);
        $this->deleteCurrencyAction->execute($currency);

        return response()->json(['message' => 'Currency deleted']);
    }
}
