<?php

namespace App\Http\Controllers\Api\Currency;

use App\Http\Controllers\Controller;
use App\Domain\Currency\Models\Currency;
use App\Domain\Currency\Resources\CurrencyResource;
use Illuminate\Http\Request;

use App\Domain\Currency\Requests\StoreCurrencyRequest;
use App\Domain\Currency\Requests\UpdateCurrencyRequest;
use App\Domain\Currency\Actions\ListCurrenciesAction;
use App\Domain\Currency\Actions\GetCurrencyAction;
use App\Domain\Currency\DataTransferObjects\CurrencyData;

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
        $currency = $this->createCurrencyAction->execute(CurrencyData::fromRequest($request->validated()));

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
        $updatedCurrency = $this->updateCurrencyAction->execute($currency, CurrencyData::fromRequest($request->validated()));

        return response()->json(['message' => 'Currency updated', 'currency' => new CurrencyResource($updatedCurrency)]);
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $currency = $this->getCurrencyAction->execute($id);
        $this->deleteCurrencyAction->execute($currency);

        return response()->json(['message' => 'Currency deleted']);
    }
}
