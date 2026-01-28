<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Services\AuthService;

class LoginAction
{
    public function __construct(
        protected AuthService $authService
    ) {
    }

    public function execute(array $credentials): array
    {
        return $this->authService->attemptLogin($credentials);
    }
}
