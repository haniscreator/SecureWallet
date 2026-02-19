<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\Transaction\Requests\ListTransactionRequest;
use App\Domain\Transaction\Actions\ListTransactionsAction;
use App\Domain\Transaction\DataTransferObjects\TransactionFilterData;
use App\Domain\Transaction\Resources\TransactionResource;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        protected ListTransactionsAction $listTransactionsAction
    ) {
    }

    public function index(ListTransactionRequest $request, $walletId)
    {
        $wallet = Wallet::findOrFail($walletId);

        if ($request->user()->cannot('view', $wallet)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $filters = TransactionFilterData::fromRequest($request);
        $transactions = $this->listTransactionsAction->execute($request->user(), $filters, $wallet);

        return TransactionResource::collection($transactions);
    }

    public function all(ListTransactionRequest $request)
    {
        $filters = TransactionFilterData::fromRequest($request);
        $transactions = $this->listTransactionsAction->execute($request->user(), $filters);

        return TransactionResource::collection($transactions);
    }

    public function show($id)
    {
        $transaction = \App\Domain\Transaction\Models\Transaction::with(['fromWallet.currency', 'toWallet.currency'])->findOrFail($id);
        // Authorization check could be added here
        return new TransactionResource($transaction);
    }

    public function dashboardTotalBalance(Request $request)
    {
        $totals = $this->listTransactionsAction->service()->getAggregatedBalances($request->user());
        return response()->json($totals);
    }
}
