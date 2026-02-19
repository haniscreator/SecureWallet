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
            $status = TransactionStatus::CODE_PENDING;
            $approvedAt = null;
            $approvedBy = null;

            if ($initiator->hasRole('admin') || $initiator->hasRole('manager')) {
                // Admin/Manager: Always Auto-Approve
                $status = TransactionStatus::CODE_COMPLETED;
                $approvedAt = now();
                $approvedBy = $initiator->id;
            } else {
                // Regular User
                if ($amount <= $limit) {
                    // Auto-Approve if within limit
                    $status = TransactionStatus::CODE_COMPLETED;
                    $approvedAt = now();
                    $approvedBy = null;
                } else {
                    $status = TransactionStatus::CODE_PENDING;
                }
            }

            $statusId = TransactionStatus::getId($status);

            // 5. Create Transaction
            return Transaction::create([
                'user_id' => $initiator->id,
                'from_wallet_id' => $fromWallet->id,
                'external_wallet_id' => $externalWalletId,
                'to_wallet_id' => $toWallet ? $toWallet->id : null,
                'amount' => $amount,
                'transaction_status_id' => $statusId,
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
            if ($transaction->transaction_status_id !== TransactionStatus::getId(TransactionStatus::CODE_PENDING)) {
                throw new Exception("Transaction is not pending.");
            }

            // Re-check if source wallet is frozen
            if ($transaction->fromWallet->isFrozen()) {
                throw new Exception("Source wallet is frozen.");
            }

            // Validate Destination Wallet (Internal)
            if ($transaction->to_wallet_id) {
                $toWallet = Wallet::find($transaction->to_wallet_id);
                if (!$toWallet) {
                    throw new Exception("Sorry, we couldn’t find the destination wallet.");
                }

                if ((int) $toWallet->currency_id !== (int) $transaction->fromWallet->currency_id) {
                    throw new Exception("Sorry, the destination wallet currency does not match.");
                }
                if (!$toWallet->isActive()) {
                    throw new Exception("Sorry, the destination wallet is not active.");
                }
            }

            // Validate Destination Wallet (External)
            if ($transaction->external_wallet_id) {
                // Determine if we need to check External Wallet validity
                // Assuming we should check existence and status if tracked
                $externalWallet = ExternalWallet::find($transaction->external_wallet_id);

                if (!$externalWallet) {
                    // Could happen if hard deleted
                    throw new Exception("Sorry, we couldn’t find the destination wallet.");
                }


                if ((int) $externalWallet->currency_id !== (int) $transaction->fromWallet->currency_id) {
                    throw new Exception("Sorry, the destination wallet currency does not match.");
                }

                if (!$externalWallet->status) {
                    throw new Exception("Sorry, the destination wallet is not active.");
                }
            }

            $completedStatusId = TransactionStatus::getId(TransactionStatus::CODE_COMPLETED);

            $transaction->update([
                'transaction_status_id' => $completedStatusId,
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
            if ($transaction->transaction_status_id !== TransactionStatus::getId(TransactionStatus::CODE_PENDING)) {
                throw new Exception("Transaction is not pending.");
            }

            $rejectedStatusId = TransactionStatus::getId(TransactionStatus::CODE_REJECTED);

            $transaction->update([
                'transaction_status_id' => $rejectedStatusId,
                'rejection_reason' => $reason,
                // Optionally track rejector
            ]);

            return $transaction->fresh();
        });
    }

    /**
     * Cancel a pending transfer by the initiator.
     *
     * @param Transaction $transaction
     * @param User $user
     * @return Transaction
     * @throws Exception
     */
    public function cancelTransfer(Transaction $transaction, User $user): Transaction
    {
        return DB::transaction(function () use ($transaction, $user) {
            if ($transaction->transaction_status_id !== TransactionStatus::getId(TransactionStatus::CODE_PENDING)) {
                throw new Exception("Only pending transactions can be cancelled.");
            }

            if ($transaction->user_id !== $user->id) {
                throw new Exception("You can only cancel your own transactions.");
            }

            $cancelledStatusId = TransactionStatus::getId(TransactionStatus::CODE_CANCELLED);

            $transaction->update([
                'transaction_status_id' => $cancelledStatusId,
            ]);

            return $transaction->fresh();
        });
    }
}
