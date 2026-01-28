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
        // validation and authService->login
    }

    public function logout(Request $request)
    {
        // authService->logout
    }
}
