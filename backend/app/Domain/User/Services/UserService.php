<?php

namespace App\Domain\User\Services;

use App\Domain\User\Models\User;
use App\Domain\User\Models\UserRole;
use Illuminate\Support\Facades\Hash;
use App\Domain\User\DataTransferObjects\UserData;

class UserService
{
    public function createUser(UserData $data): User
    {
        $roleName = $data->role ?? 'user';
        $role = UserRole::where('name', $roleName)->first();

        $user = User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password),
            'role_id' => $role?->id,
            'status' => $data->status ?? true,
        ]);

        if ($data->wallet_ids !== null) {
            $user->wallets()->sync($data->wallet_ids);
        }

        return $user;
    }

    public function listUsers()
    {
        return User::with('wallets')->get();
    }

    public function updateUser(User $user, UserData $data): User
    {
        $updateData = $data->toArray();

        // Remove 'role' from array as it is not a column
        unset($updateData['role']);

        if ($data->role) {
            $role = UserRole::where('name', $data->role)->first();
            if ($role) {
                $updateData['role_id'] = $role->id;
            }
        }

        $user->update($updateData);

        if ($data->wallet_ids !== null) {
            $user->wallets()->sync($data->wallet_ids);
        }

        return $user;
    }

    public function deleteUser(User $user): bool
    {
        return $user->delete();
    }
}
