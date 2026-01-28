<?php

namespace App\Domain\User\Services;

use App\Domain\User\Actions\CreateMemberAction;
use App\Domain\User\Actions\AssignWalletAction;

class UserService
{
    public function __construct(
        protected CreateMemberAction $createMemberAction,
        protected AssignWalletAction $assignWalletAction
    ) {
    }

    // Service methods
}
