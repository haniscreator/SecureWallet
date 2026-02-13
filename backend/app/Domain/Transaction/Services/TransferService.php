<?php

namespace App\Domain\Transaction\Services;

use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transaction\Models\TransactionStatus;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\Wallet\Models\ExternalWallet;
use App\Domain\Setting\Models\Setting;
use App\Domain\User\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class TransferService
{
    /**
     * Initiate a transfer to an external wallet.
     *
     * @param Wallet $fromWallet
     * @param ExternalWallet $toWallet
     * @param float $amount
     * @return Transaction
     * @throws Exception
     */
    public function initiateTransfer(Wallet $fromWallet, ExternalWallet $toWallet, float $amount, User $initiator): Transaction
    {
        return DB::transaction(function () use ($fromWallet, $toWallet, $amount, $initiator) {
            // 1. Check if currencies match
            if ($fromWallet->currency_id !== $toWallet->currency_id) {
                throw new Exception("Currency mismatch between source and target wallets.");
            }

            // 2. Check Global Transfer Limit
            $limitSetting = Setting::where('key', 'transfer_limit')->first();
            $limit = $limitSetting ? (float) $limitSetting->value : 1000.0;

            if ($amount > $limit) {
                // Should we reject or just fail? Usually existing validation fails.
                // throw new Exception("Transfer amount exceeds the global limit of {$limit}.");
                // Requirement Change: Auto-approve if <= limit, else Pending (for users).
                // Admin/Manager always auto-approve.
            }

            // 3. Check Available Balance
            if ($fromWallet->available_balance < $amount) {
                throw new Exception("Insufficient funds.");
            }

            // 4. Determine Status
            $status = 'pending';
            $approvedAt = null;
            $approvedBy = null;

            if ($initiator->hasRole('admin') || $initiator->hasRole('manager')) {
                // Admin/Manager: Always Auto-Approve
                $status = 'completed';
                $approvedAt = now();
                $approvedBy = $initiator->id;
            } else {
                // Regular User
                if ($amount <= $limit) {
                    // Auto-Approve if within limit
                    $status = 'completed';
                    $approvedAt = now();
                    $approvedBy = null; // System auto-approval? Or keep null.
                } else {
                    $status = 'pending';
                }
            }

            $statusModel = TransactionStatus::where('code', $status)->firstOrFail();

            // 5. Create Transaction
            return Transaction::create([
                'user_id' => $initiator->id,
                'from_wallet_id' => $fromWallet->id,
                'external_wallet_id' => $toWallet->id,
                'to_wallet_id' => null, // External transfer
                'amount' => $amount,
                'transaction_status_id' => $statusModel->id,
                'type' => 'debit',
                'created_at' => now(),
                'approved_by' => $approvedBy,
                'approved_at' => $approvedAt,
            ]);
        });
    }

    /**
     * Approve a pending transfer.
     *
     * @param Transaction $transaction
     * @param User $approver
     * @return Transaction
     * @throws Exception
     */
    public function approveTransfer(Transaction $transaction, User $approver): Transaction
    {
        return DB::transaction(function () use ($transaction, $approver) {
            if ($transaction->status->code !== 'pending') {
                throw new Exception("Transaction is not pending.");
            }

            // Re-check if wallet is frozen
            if ($transaction->fromWallet->isFrozen()) {
                throw new Exception("Source wallet is frozen.");
            }

            // We do not strictly re-check available balance here because the funds were "reserved" 
            // by virtue of the transaction being 'pending' and counting against 'available_balance' 
            // for OTHER transactions.
            // However, we should ensure the wallet still has enough TOTAL balance to cover this 
            // (in case funds were drained via other means that bypassed checks).

            // For now, we trust the flow.

            $completedStatus = TransactionStatus::where('code', 'completed')->firstOrFail();

            $transaction->update([
                'transaction_status_id' => $completedStatus->id,
                'approved_by' => $approver->id,
                'approved_at' => now(),
            ]);

            return $transaction->fresh();
        });
    }

    /**
     * Reject a pending transfer.
     *
     * @param Transaction $transaction
     * @param User $rejector
     * @param string $reason
     * @return Transaction
     * @throws Exception
     */
    public function rejectTransfer(Transaction $transaction, User $rejector, string $reason): Transaction
    {
        return DB::transaction(function () use ($transaction, $rejector, $reason) {
            if ($transaction->status->code !== 'pending') {
                throw new Exception("Transaction is not pending.");
            }

            $rejectedStatus = TransactionStatus::where('code', 'rejected')->firstOrFail();

            $transaction->update([
                'transaction_status_id' => $rejectedStatus->id,
                'rejection_reason' => $reason,
                // Optionally track rejector
            ]);

            return $transaction->fresh();
        });
    }
}
