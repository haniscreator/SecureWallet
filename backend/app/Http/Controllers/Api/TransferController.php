<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\Transaction\Services\TransferService;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\Wallet\Models\ExternalWallet;
use App\Domain\Transaction\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TransferController extends Controller
{
    protected $transferService;

    public function __construct(TransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    public function initiate(Request $request)
    {
        $request->validate([
            'source_wallet_id' => 'required|exists:wallets,id',
            'external_wallet_id' => 'required|exists:external_wallets,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $user = Auth::user();
        $sourceWallet = Wallet::findOrFail($request->source_wallet_id);

        // Authorization: Check if user owns the wallet
        // Assuming wallet belongs to user (many-to-many or one-to-many)
        if (!$sourceWallet->users->contains($user->id)) {
            return response()->json(['message' => 'Unauthorized access to wallet.'], 403);
        }

        $externalWallet = ExternalWallet::findOrFail($request->external_wallet_id);

        try {
            $transaction = $this->transferService->initiateTransfer(
                $sourceWallet,
                $externalWallet,
                (float) $request->amount
            );

            return response()->json([
                'message' => 'Transfer initiated successfully.',
                'transaction' => $transaction
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function approve(Transaction $transaction)
    {
        $user = Auth::user();

        // Authorization: Check if user is Manager (implied by route middleware or specific gate)
        // Here we can use Gate or Policy.
        // For simplicity, we check role directly or rely on route middleware.
        // "manager" role check.
        if (!$user->tokenCan('role:manager') && !$user->hasRole('manager')) {
            // Or verify with Policy
            // Ensure this user CAN approve.
        }

        // Let's use Policy if we have one, or just check role for now as per requirements.
        // "Implement approve_transfer policy/permission" was done in Phase 2.
        // Let's use Gate::authorize('approve', $transaction) if Policy exists.
        // For now, explicit check:
        // Adjust based on how roles are implemented (User::hasRole).

        if (!$user->hasRole('manager') && !$user->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized. Manager role required.'], 403);
        }

        try {
            $approvedTransaction = $this->transferService->approveTransfer($transaction, $user);
            return response()->json([
                'message' => 'Transfer approved successfully.',
                'transaction' => $approvedTransaction
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function reject(Request $request, Transaction $transaction)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        if (!$user->hasRole('manager') && !$user->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized. Manager role required.'], 403);
        }

        try {
            $rejectedTransaction = $this->transferService->rejectTransfer(
                $transaction,
                $user,
                $request->reason
            );
            return response()->json([
                'message' => 'Transfer rejected successfully.',
                'transaction' => $rejectedTransaction
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
