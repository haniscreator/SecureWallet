<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Domain\Auth\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $data = $this->authService->login($credentials);

        return response()->json([
            'message' => 'Login successful',
            'user' => $data['user'],
            'token' => $data['token'],
        ]);
    }

    public function logout(Request $request)
    {
        $this->authService->logout();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }
}
