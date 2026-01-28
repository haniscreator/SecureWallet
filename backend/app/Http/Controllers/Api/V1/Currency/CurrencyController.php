<?php

namespace App\Http\Controllers\Api\V1\Currency;

use App\Http\Controllers\Controller;
use App\Domain\Currency\Models\Currency;
use App\Http\Resources\CurrencyResource;
use Illuminate\Http\Request;

use App\Http\Requests\Currency\StoreCurrencyRequest;
use App\Http\Requests\Currency\UpdateCurrencyRequest;
use App\Domain\Currency\Actions\ListCurrenciesAction;
use App\Domain\Currency\Actions\GetCurrencyAction;

class CurrencyController extends Controller
{
    public function __construct(
        protected \App\Domain\Currency\Actions\CreateCurrencyAction $createCurrencyAction,
        protected \App\Domain\Currency\Actions\UpdateCurrencyAction $updateCurrencyAction,
        protected \App\Domain\Currency\Actions\DeleteCurrencyAction $deleteCurrencyAction,
        protected ListCurrenciesAction $listCurrenciesAction,
        protected GetCurrencyAction $getCurrencyAction
    ) {
    }

    public function index(Request $request)
    {
        return CurrencyResource::collection($this->listCurrenciesAction->execute());
    }

    public function show(Request $request, $id)
    {
        return new CurrencyResource($this->getCurrencyAction->execute($id));
    }

    public function store(StoreCurrencyRequest $request)
    {
        $currency = $this->createCurrencyAction->execute($request->validated());

        return response()->json(['message' => 'Currency created', 'currency' => new CurrencyResource($currency)], 201);
    }

    public function update(UpdateCurrencyRequest $request, $id)
    {
        // For update action, we need the currency model. 
        // We can either fetch it here via GetCurrencyAction or Service, or let Action fetch it.
        // Based on previous pattern (updateAction takes Currency model), we fetch it first.
        // Let's use getCurrencyAction for consistency or just findOrFail.
        // Since GetCurrencyAction returns Currency, let's use it.

        $currency = $this->getCurrencyAction->execute($id);
        $updatedCurrency = $this->updateCurrencyAction->execute($currency, $request->validated());

        return response()->json(['message' => 'Currency updated', 'currency' => new CurrencyResource($updatedCurrency)]);
    }

    public function destroy(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $currency = $this->getCurrencyAction->execute($id);
        $this->deleteCurrencyAction->execute($currency);

        return response()->json(['message' => 'Currency deleted']);
    }
}
