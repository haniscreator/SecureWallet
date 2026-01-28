<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Services\AuthService;

class LogoutAction
{
    public function __construct(
        protected AuthService $authService
    ) {
    }

    public function execute(): void
    {
        $this->authService->logoutUser();
    }
}
