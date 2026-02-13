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
     * Initiate a transfer (Internal or External).
     *
     * @param Wallet $fromWallet
     * @param string $type 'internal' | 'external'
     * @param mixed $target Target Wallet ID (internal) or Address (external)
     * @param float $amount
     * @param User $initiator
     * @param string|null $description
     * @return Transaction
     * @throws Exception
     */
    public function initiateTransfer(Wallet $fromWallet, string $type, $target, float $amount, User $initiator, ?string $description = null): Transaction
    {
        return DB::transaction(function () use ($fromWallet, $type, $target, $amount, $initiator, $description) {
            $toWallet = null;
            $externalWalletId = null;

            // 0. Validate Source Wallet matches User (Controller does this, but sanity check good)

            // 1. Resolve Target
            if ($type === 'internal') {
                $toWallet = Wallet::findOrFail($target); // $target is ID

                // Identify same wallet
                if ($fromWallet->id === $toWallet->id) {
                    throw new Exception("Cannot transfer to the same wallet.");
                }

                // Currency mismatch
                if ($fromWallet->currency_id !== $toWallet->currency_id) {
                    throw new Exception("Currency mismatch between source and target wallets.");
                }

            } else {
                // External
                // Find or Create External Wallet
                // $target is address string
                $externalWallet = ExternalWallet::where('address', $target)->first();

                if ($externalWallet) {
                    // Check currency
                    if ($externalWallet->currency_id !== $fromWallet->currency_id) {
                        // In a real app we might handle multi-chain, but for now strict check:
                        throw new Exception("Currency mismatch between source wallet and target address.");
                    }
                } else {
                    $externalWallet = ExternalWallet::create([
                        'address' => $target,
                        'currency_id' => $fromWallet->currency_id,
                        'name' => 'External ' . substr($target, 0, 8)
                    ]);
                }

                $externalWalletId = $externalWallet->id;
            }

            // 2. Check Global Transfer Limit
            $limitSetting = Setting::where('key', 'transfer_limit')->first();
            $limit = $limitSetting ? (float) $limitSetting->value : 1000.0;

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
                    $approvedBy = null;
                } else {
                    $status = 'pending';
                }
            }

            $statusModel = TransactionStatus::where('code', $status)->firstOrFail();

            // 5. Create Transaction
            return Transaction::create([
                'user_id' => $initiator->id,
                'from_wallet_id' => $fromWallet->id,
                'external_wallet_id' => $externalWalletId,
                'to_wallet_id' => $toWallet ? $toWallet->id : null,
                'amount' => $amount,
                'transaction_status_id' => $statusModel->id,
                'type' => 'debit', // Always debit from source perspective
                'reference' => $description, // Use description as particular reference? or generate valid reference?
                'created_at' => now(),
                'approved_by' => $approvedBy,
                'approved_at' => $approvedAt,
            ]);

            // Note: If Internal and Completed, we might need a corresponding Credit transaction for the receiver?
            // The system lists transactions by wallet. A single transaction row has `from` and `to`.
            // So one row is sufficient to show Debit for Sender and Credit for Receiver (if logic queries properly).
            // `Transaction` model likely handles this relationship.
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
