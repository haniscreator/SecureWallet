<?php

namespace App\Domain\User\Actions;

use App\Domain\User\Models\User;
use App\Domain\User\Services\UserService;
use App\Domain\User\DataTransferObjects\UserData;

class CreateMemberAction
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    public function execute(UserData $data): User
    {
        return $this->userService->createUser($data);
    }
}
