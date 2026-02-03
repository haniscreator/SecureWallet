<?php

namespace App\Domain\Auth\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Domain\Auth\DataTransferObjects\LoginData;

class AuthService
{
    public function attemptLogin(LoginData $data): array
    {
        if (!Auth::attempt($data->toArray())) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logoutUser(): void
    {
        Auth::user()->currentAccessToken()->delete();
    }
}
