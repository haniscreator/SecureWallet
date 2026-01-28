<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Domain\Auth\Actions\LoginAction;
use App\Domain\Auth\Actions\LogoutAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected LoginAction $loginAction,
        protected LogoutAction $logoutAction
    ) {
    }

    public function login(LoginRequest $request)
    {
        $data = $this->loginAction->execute(\App\Domain\Auth\DataTransferObjects\LoginData::fromRequest($request->validated()));

        return response()->json([
            'message' => 'Login successful',
            'user' => new \App\Http\Resources\UserResource($data['user']),
            'token' => $data['token'],
        ]);
    }

    public function logout(Request $request)
    {
        $this->logoutAction->execute();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }
}
