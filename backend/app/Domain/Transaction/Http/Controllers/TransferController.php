<?php

namespace App\Domain\Transaction\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Transaction\Services\TransferService;
use App\Domain\Transaction\Http\Requests\InitiateTransferRequest;
use App\Domain\Transaction\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class TransferController extends Controller
{
    protected TransferService $transferService;

    public function __construct(TransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    public function initiate(InitiateTransferRequest $request): JsonResponse
    {
        try {
            $transaction = $this->transferService->initiateTransfer($request->user(), $request->validated());

            return response()->json([
                'message' => 'Transfer initiated successfully.',
                'data' => $transaction->load(['status', 'toWallet.users']),
                'status' => $transaction->status->name
            ], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function approve(Request $request, Transaction $transaction): JsonResponse
    {
        // Policy check should be done via middleware or strictly here.
        // Assuming we use 'can:approve,transaction' middleware or similar in routes,
        // OR we check manually.
        // Let's check manual policy for now or use Gate.

        $this->authorize('approve', $transaction);

        try {
            $this->transferService->approveTransfer($request->user(), $transaction);

            return response()->json([
                'message' => 'Transfer approved successfully.',
                'status' => 'completed'
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function reject(Request $request, Transaction $transaction): JsonResponse
    {
        $this->authorize('approve', $transaction); // Re-use approve policy for rejection rights

        $request->validate(['reason' => 'required|string|max:255']);

        try {
            $this->transferService->rejectTransfer($request->user(), $transaction, $request->reason);

            return response()->json([
                'message' => 'Transfer rejected.',
                'status' => 'rejected'
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
