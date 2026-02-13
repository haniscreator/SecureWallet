<?php

namespace App\Domain\Transaction\Policies;

use App\Domain\Transaction\Models\Transaction;
use App\Domain\User\Models\User;
use Illuminate\Auth\Access\Response;

class TransactionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        // Check if user owns one of the wallets involved
        $isOwner = $user->wallets()->whereIn('id', [$transaction->from_wallet_id, $transaction->to_wallet_id])->exists();

        return $isOwner || $user->hasRole('admin') || $user->hasRole('manager');
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user): bool
    {
        return $user->hasRole('manager') || $user->hasRole('admin');
    }
}
