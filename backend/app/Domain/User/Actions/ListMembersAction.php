<?php

namespace App\Domain\User\Actions;

use App\Domain\User\Services\UserService;
use Illuminate\Database\Eloquent\Collection;

class ListMembersAction
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    public function execute(): Collection
    {
        return $this->userService->listUsers();
    }
}
