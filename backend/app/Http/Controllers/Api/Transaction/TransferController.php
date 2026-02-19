<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Domain\Transaction\Actions\InitiateTransferAction;
use App\Domain\Transaction\Actions\ApproveTransferAction;
use App\Domain\Transaction\Actions\RejectTransferAction;
use App\Domain\Transaction\Actions\CancelTransferAction;
use App\Domain\Transaction\DataTransferObjects\TransferData;
use App\Domain\Transaction\Requests\InitiateTransferRequest;
use App\Domain\Transaction\Requests\RejectTransferRequest;
use App\Domain\Transaction\Resources\TransactionResource;
use App\Domain\Transaction\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransferController extends Controller
{
    public function __construct(
        protected InitiateTransferAction $initiateTransferAction,
        protected ApproveTransferAction $approveTransferAction,
        protected RejectTransferAction $rejectTransferAction,
        protected CancelTransferAction $cancelTransferAction,
    ) {
    }

    public function initiate(InitiateTransferRequest $request)
    {
        try {
            $data = TransferData::fromRequest($request->validated());
            $transaction = $this->initiateTransferAction->execute($data, Auth::user());

            return response()->json([
                'message' => 'Transfer initiated successfully.',
                'transaction' => new TransactionResource($transaction)
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function approve(Transaction $transaction)
    {
        $user = Auth::user();

        if (!$user->hasRole('manager') && !$user->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized. Manager role required.'], 403);
        }

        try {
            $approvedTransaction = $this->approveTransferAction->execute($transaction, $user);
            return response()->json([
                'message' => 'Transfer approved successfully.',
                'transaction' => new TransactionResource($approvedTransaction)
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function reject(RejectTransferRequest $request, Transaction $transaction)
    {
        $user = Auth::user();

        try {
            $rejectedTransaction = $this->rejectTransferAction->execute(
                $transaction,
                $user,
                $request->validated()['reason']
            );
            return response()->json([
                'message' => 'Transfer rejected successfully.',
                'transaction' => new TransactionResource($rejectedTransaction)
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function cancel(Transaction $transaction)
    {
        $user = Auth::user();

        try {
            $cancelledTransaction = $this->cancelTransferAction->execute($transaction, $user);
            return response()->json([
                'message' => 'Transfer cancelled successfully.',
                'transaction' => new TransactionResource($cancelledTransaction)
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
