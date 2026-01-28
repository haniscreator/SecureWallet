<?php

namespace Tests\Unit\Domain\User\Services;

use App\Domain\User\Models\User;
use App\Domain\User\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserService();
    }

    public function test_create_user_success()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => 'admin',
        ];

        $user = $this->service->createUser($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'role' => 'admin',
        ]);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_create_user_defaults_to_user_role()
    {
        $data = [
            'name' => 'Web User',
            'email' => 'web@example.com',
            'password' => 'password123',
        ];

        $user = $this->service->createUser($data);

        $this->assertEquals('user', $user->role);
    }

    public function test_list_users()
    {
        User::factory()->count(3)->create();

        $users = $this->service->listUsers();

        $this->assertCount(3, $users);
    }

    public function test_update_user()
    {
        $user = User::factory()->create(['name' => 'Old Name']);

        $updatedUser = $this->service->updateUser($user, ['name' => 'New Name']);

        $this->assertEquals('New Name', $updatedUser->name);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
        ]);
    }

    public function test_delete_user()
    {
        $user = User::factory()->create();

        $result = $this->service->deleteUser($user);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
