<?php

namespace App\Policies\Wallet;

use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\Wallet;

class WalletPolicy
{
    public function viewAny(User $user): bool
    {
        // Admins can see "all", but this check authorizes the *concept* of viewing wallets.
        // Usually true for everyone, but the Service will filter the list.
        return true;
    }

    public function view(User $user, Wallet $wallet): bool
    {
        return $user->role === 'admin' || $wallet->users()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Wallet $wallet): bool
    {
        return $user->role === 'admin';
    }

    // Custom ability to freeze
    public function freeze(User $user, Wallet $wallet): bool
    {
        return $user->role === 'admin';
    }

    // Custom ability to add members
    public function assignMember(User $user, Wallet $wallet): bool
    {
        return $user->role === 'admin';
    }
}
