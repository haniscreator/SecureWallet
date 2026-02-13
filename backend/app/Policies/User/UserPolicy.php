<?php

namespace App\Policies\User;

use App\Domain\User\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, User $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function delete(User $user, User $model): bool
    {
        return $user->hasRole('admin'); // Only admin can delete users
    }
}
