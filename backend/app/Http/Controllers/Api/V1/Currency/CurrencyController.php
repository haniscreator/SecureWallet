<?php

namespace App\Http\Controllers\Api\V1\Currency;

use App\Http\Controllers\Controller;
use App\Domain\Currency\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    // Simple CRUD - straightforward enough to put in Controller or separate generic Service
    // Following Action logic might be overkill for simple CRUD lookup unless we have complex validation logic?
    // Let's stick to simple Eloquent for Settings.

    public function index(Request $request)
    {
        // Public or Authenticated? Probably authenticated users can see available currencies
        // Admin can see all, maybe inactive ones too?
        return response()->json(Currency::all());
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
        ]);

        $currency = Currency::create($validated);

        return response()->json(['message' => 'Currency created', 'currency' => $currency], 201);
    }

    public function update(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $currency = Currency::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'symbol' => 'nullable|string|max:10',
            'status' => 'sometimes|boolean',
        ]);

        $currency->update($validated);

        return response()->json(['message' => 'Currency updated', 'currency' => $currency]);
    }

    public function destroy(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $currency = Currency::findOrFail($id);
        // Maybe check if used in wallets?
        // simple delete for now
        $currency->delete();

        return response()->json(['message' => 'Currency deleted']);
    }
}
