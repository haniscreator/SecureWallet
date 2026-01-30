<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Domain\User\Requests\StoreMemberRequest;
use App\Domain\User\Requests\UpdateMemberRequest;
use App\Domain\User\Actions\ListMembersAction;
use App\Domain\User\Actions\CreateMemberAction;
use App\Domain\User\Actions\UpdateMemberAction;
use App\Domain\User\Actions\DeleteMemberAction;
use App\Domain\User\Actions\GetMemberAction;

use App\Domain\User\Resources\UserResource;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function __construct(
        protected ListMembersAction $listMembersAction,
        protected GetMemberAction $getMemberAction,
        protected CreateMemberAction $createMemberAction,
        protected UpdateMemberAction $updateMemberAction,
        protected DeleteMemberAction $deleteMemberAction
    ) {
    }

    public function index(Request $request)
    {
        if ($request->user()->cannot('viewAny', \App\Domain\User\Models\User::class)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return UserResource::collection($this->listMembersAction->execute());
    }

    public function show(Request $request, $id)
    {
        $user = $this->getMemberAction->execute($id);

        if ($request->user()->cannot('view', $user)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(['user' => new UserResource($user)]);
    }

    public function store(StoreMemberRequest $request)
    {
        $user = $this->createMemberAction->execute(\App\Domain\User\DataTransferObjects\UserData::fromRequest($request->validated()));

        return response()->json([
            'message' => 'Member created successfully',
            'user' => new UserResource($user),
        ], 201);
    }

    public function update(UpdateMemberRequest $request, $id)
    {
        $user = $this->getMemberAction->execute($id);

        $updatedUser = $this->updateMemberAction->execute($user, \App\Domain\User\DataTransferObjects\UserData::fromRequest($request->validated()));

        return response()->json([
            'message' => 'Member updated successfully',
            'user' => new UserResource($updatedUser),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $this->getMemberAction->execute($id);

        if ($request->user()->cannot('delete', $user)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->deleteMemberAction->execute($user);

        return response()->json(['message' => 'Member deleted successfully']);
    }
}
