<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Services\AuthService;
use App\Domain\Auth\DataTransferObjects\LoginData;

class LoginAction
{
    public function __construct(
        protected AuthService $authService
    ) {
    }

    public function execute(LoginData $data): array
    {
        return $this->authService->attemptLogin($data);
    }
}
