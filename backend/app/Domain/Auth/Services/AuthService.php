<?php

namespace App\Domain\Auth\Services;

use App\Domain\Auth\Actions\LoginAction;
use App\Domain\Auth\Actions\LogoutAction;

class AuthService
{
    public function __construct(
        protected LoginAction $loginAction,
        protected LogoutAction $logoutAction
    ) {
    }

    public function login(array $credentials): array
    {
        return $this->loginAction->execute($credentials);
    }

    public function logout(): void
    {
        $this->logoutAction->execute();
    }
}
