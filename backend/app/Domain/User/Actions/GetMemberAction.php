<?php

namespace App\Domain\User\Actions;

use App\Domain\User\Models\User;
use App\Domain\User\Services\UserService;

class GetMemberAction
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    public function execute(string $id): User
    {
        return User::findOrFail($id);
    }
}
