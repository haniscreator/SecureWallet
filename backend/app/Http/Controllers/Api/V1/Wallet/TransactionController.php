<?php

namespace App\Http\Controllers\Api\V1\Wallet;

use App\Http\Controllers\Controller;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\Wallet\Services\TransactionService;
use App\Domain\Wallet\Resources\TransactionResource;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        protected TransactionService $transactionService
    ) {
    }

    public function index(Request $request, $walletId)
    {
        $wallet = Wallet::findOrFail($walletId);

        if ($request->user()->cannot('view', $wallet)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'type' => 'nullable|in:credit,debit',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
        ]);

        $transactions = $this->transactionService->listTransactions($wallet, $validated);

        return TransactionResource::collection($transactions);
    }
}
