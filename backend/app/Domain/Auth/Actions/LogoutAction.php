<?php

namespace App\Domain\Auth\Actions;

use Illuminate\Support\Facades\Auth;

class LogoutAction
{
    public function execute(): void
    {
        Auth::user()->currentAccessToken()->delete();
    }
}
