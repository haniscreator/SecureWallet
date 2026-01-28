<?php

namespace Tests\Unit\Domain\Auth\Services;

use App\Domain\Auth\Services\AuthService;
use App\Domain\User\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_attempt_login_success()
    {
        $credentials = new \App\Domain\Auth\DataTransferObjects\LoginData(
            email: 'test@example.com',
            password: 'password'
        );
        $user = Mockery::mock(User::class);
        $user->shouldReceive('createToken')->andReturn((object) ['plainTextToken' => 'test-token']);

        Auth::shouldReceive('attempt')
            ->once()
            ->with($credentials->toArray())
            ->andReturn(true);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($user);

        $service = new AuthService();
        $result = $service->attemptLogin($credentials);

        $this->assertEquals($user, $result['user']);
        $this->assertEquals('test-token', $result['token']);
    }

    public function test_attempt_login_failure()
    {
        $credentials = new \App\Domain\Auth\DataTransferObjects\LoginData(
            email: 'wrong@example.com',
            password: 'wrong'
        );

        Auth::shouldReceive('attempt')
            ->once()
            ->with($credentials->toArray())
            ->andReturn(false);

        $this->expectException(ValidationException::class);

        $service = new AuthService();
        $service->attemptLogin($credentials);
    }

    public function test_logout_user()
    {
        $user = Mockery::mock(User::class);
        $accessToken = Mockery::mock();
        $accessToken->shouldReceive('delete')->once();

        $user->shouldReceive('currentAccessToken')
            ->once()
            ->andReturn($accessToken);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($user);

        $service = new AuthService();
        $service->logoutUser();

        // Assertion is implicit via Mockery expectations
        $this->assertTrue(true);
    }
}
