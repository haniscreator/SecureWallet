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
        $data = new \App\Domain\User\DataTransferObjects\UserData(
            name: 'Test User',
            email: 'test@example.com',
            password: 'password123',
            role: 'admin'
        );

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
        $data = new \App\Domain\User\DataTransferObjects\UserData(
            name: 'Web User',
            email: 'web@example.com',
            password: 'password123'
        );

        $user = $this->service->createUser($data);

        $this->assertEquals('user', $user->role);
    }

    public function test_list_users()
    {
        User::factory()->count(3)->create();

        $users = $this->service->listUsers();

        $this->assertCount(3, $users);
    }

    public function test_create_user_with_wallets()
    {
        $wallet1 = \App\Domain\Wallet\Models\Wallet::factory()->create();
        $wallet2 = \App\Domain\Wallet\Models\Wallet::factory()->create();

        $data = new \App\Domain\User\DataTransferObjects\UserData(
            name: 'Wallet User',
            email: 'wallet@example.com',
            password: 'password123',
            wallet_ids: [$wallet1->id, $wallet2->id]
        );

        $user = $this->service->createUser($data);

        $this->assertCount(2, $user->wallets);
        $this->assertTrue($user->wallets->contains($wallet1));
        $this->assertTrue($user->wallets->contains($wallet2));
    }

    public function test_update_user_synch_wallets()
    {
        $user = User::factory()->create();
        $wallet = \App\Domain\Wallet\Models\Wallet::factory()->create();

        $data = new \App\Domain\User\DataTransferObjects\UserData(
            name: 'Updated User',
            wallet_ids: [$wallet->id]
        );

        $updatedUser = $this->service->updateUser($user, $data);

        $this->assertCount(1, $updatedUser->wallets);
        $this->assertTrue($updatedUser->wallets->contains($wallet));
    }

    public function test_update_user()
    {
        $user = User::factory()->create(['name' => 'Old Name', 'email' => 'old@example.com']);

        $data = new \App\Domain\User\DataTransferObjects\UserData(
            name: 'New Name',
            email: 'old@example.com' // Email required in DTO constructor but not modified here
        );

        $updatedUser = $this->service->updateUser($user, $data);

        $this->assertEquals('New Name', $updatedUser->name);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
        ]);
    }

    public function test_update_user_partial()
    {
        $user = User::factory()->create(['name' => 'Old Name', 'email' => 'old@example.com']);

        // Only update name
        $data = new \App\Domain\User\DataTransferObjects\UserData(
            name: 'New Name'
        );

        $updatedUser = $this->service->updateUser($user, $data);

        $this->assertEquals('New Name', $updatedUser->name);
        $this->assertEquals('old@example.com', $updatedUser->email); // Should remain unchanged
    }

    public function test_delete_user()
    {
        $user = User::factory()->create();

        $result = $this->service->deleteUser($user);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
