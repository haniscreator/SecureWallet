<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Domain\User\Actions\CreateMemberAction;
use App\Domain\User\Services\UserService;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function __construct(
        protected CreateMemberAction $createMemberAction,
        protected UserService $userService
    ) {
    }


    public function index(Request $request)
    {
        if ($request->user()->cannot('viewAny', \App\Domain\User\Models\User::class)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($this->userService->listUsers());
    }

    public function store(Request $request)
    {
        if ($request->user()->cannot('create', \App\Domain\User\Models\User::class)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'nullable|string|in:user,admin',
        ]);

        $user = $this->createMemberAction->execute($validated);

        return response()->json([
            'message' => 'Member created successfully',
            'user' => $user,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = \App\Domain\User\Models\User::findOrFail($id);

        if ($request->user()->cannot('update', $user)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'role' => 'nullable|string|in:user,admin',
        ]);

        $updatedUser = $this->userService->updateUser($user, $validated);

        return response()->json([
            'message' => 'Member updated successfully',
            'user' => $updatedUser,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = \App\Domain\User\Models\User::findOrFail($id);

        if ($request->user()->cannot('delete', $user)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->userService->deleteUser($user);

        return response()->json(['message' => 'Member deleted successfully']);
    }
}
