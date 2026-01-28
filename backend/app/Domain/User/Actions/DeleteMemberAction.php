<?php

namespace App\Domain\User\Actions;

use App\Domain\User\Models\User;
use App\Domain\User\Services\UserService;

class DeleteMemberAction
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    public function execute(User $user): bool
    {
        return $this->userService->deleteUser($user);
    }
}
