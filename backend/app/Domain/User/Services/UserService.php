<?php

namespace App\Domain\User\Services;

use App\Domain\User\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'user',
        ]);
    }

    public function listUsers()
    {
        return User::all();
    }

    public function updateUser(User $user, array $data): User
    {
        $user->update($data);
        return $user;
    }

    public function deleteUser(User $user): bool
    {
        return $user->delete();
    }
}
