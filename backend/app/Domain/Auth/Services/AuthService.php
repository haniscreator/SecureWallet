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

    // Service methods that use actions
}
