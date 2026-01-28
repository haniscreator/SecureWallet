<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Domain\User\Services\UserService;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    public function store(Request $request)
    {
        // Validation and userService->createMember
    }
}
