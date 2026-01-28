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

    public function createMember(array $data)
    {
        return $this->createMemberAction->execute($data);
    }

    public function listMembers()
    {
        return \App\Domain\User\Models\User::all();
    }

    public function updateMember(\App\Domain\User\Models\User $user, array $data)
    {
        $user->update($data);
        return $user;
    }

    public function deleteMember(\App\Domain\User\Models\User $user)
    {
        return $user->delete();
    }
}
