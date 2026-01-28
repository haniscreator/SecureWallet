<?php

namespace App\Domain\User\Actions;

use App\Domain\User\Models\User;
use App\Domain\User\Services\UserService;

class UpdateMemberAction
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    public function execute(User $user, array $data): User
    {
        return $this->userService->updateUser($user, $data);
    }
}
