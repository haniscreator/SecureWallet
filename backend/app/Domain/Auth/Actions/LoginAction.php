<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Services\AuthService;

class LoginAction
{
    public function __construct(
        protected AuthService $authService
    ) {
    }

    public function execute(\App\Domain\Auth\DataTransferObjects\LoginData $data): array
    {
        return $this->authService->attemptLogin($data);
    }
}
