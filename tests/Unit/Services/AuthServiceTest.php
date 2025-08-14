<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\AuthService;
use App\Services\EmailService;
use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $authService;
    protected $emailService;
    protected $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->emailService = Mockery::mock(EmailService::class);
        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->authService = new AuthService($this->emailService, $this->userRepository);
    }

    public function test_register_creates_user_and_sends_verification()
    {
        $userData = [
            'name' => 'Test User',
            'account' => 'test@example.com',
            'password' => 'password123',
            'phone' => '0987654321'
        ];

        $expectedData = [
            'name' => 'Test User',
            'account' => 'test@example.com',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'prvilige' => 'B',
            'status' => 'inactive',
            'phone' => '0987654321',
            'veri_code' => Mockery::any(),
            'veri_expire' => Mockery::any()
        ];

        $user = new User($expectedData);

        $this->userRepository->shouldReceive('create')
            ->once()
            ->andReturn($user);

        $this->emailService->shouldReceive('sendVerificationEmail')
            ->once()
            ->with($user->email, Mockery::any());

        $result = $this->authService->register($userData);

        $this->assertEquals($user, $result);
        $this->assertEquals('inactive', $result->status);
    }

    public function test_register_handles_admin_prefix()
    {
        $userData = [
            'name' => 'Admin User',
            'account' => 'admin./test@example.com',
            'password' => 'password123'
        ];

        $expectedData = [
            'name' => 'Admin User',
            'account' => 'test@example.com',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'prvilige' => 'A',
            'status' => 'inactive',
            'phone' => '0987654321',
            'veri_code' => Mockery::any(),
            'veri_expire' => Mockery::any()
        ];

        $user = new User($expectedData);

        $this->userRepository->shouldReceive('create')
            ->once()
            ->andReturn($user);

        $this->emailService->shouldReceive('sendVerificationEmail')
            ->once();

        $result = $this->authService->register($userData);

        $this->assertEquals('A', $result->prvilige);
    }

    public function test_authenticate_with_valid_credentials()
    {
        $credentials = [
            'account' => 'test@example.com',
            'password' => 'password123'
        ];

        $user = new User([
            'account' => 'test@example.com',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active'
        ]);

        $this->userRepository->shouldReceive('findByEmail')
            ->with('test@example.com')
            ->once()
            ->andReturn($user);

        $result = $this->authService->authenticate($credentials);

        $this->assertTrue($result);
    }

    public function test_authenticate_with_invalid_credentials()
    {
        $credentials = [
            'account' => 'test@example.com',
            'password' => 'wrongpassword'
        ];

        $user = new User([
            'account' => 'test@example.com',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active'
        ]);

        $this->userRepository->shouldReceive('findByEmail')
            ->with('test@example.com')
            ->once()
            ->andReturn($user);

        $result = $this->authService->authenticate($credentials);

        $this->assertFalse($result);
    }

    public function test_verify_code_success()
    {
        $user = new User([
            'account' => 'test@example.com',
            'email' => 'test@example.com',
            'veri_code' => '123456',
            'veri_expire' => now()->addMinutes(5),
            'status' => 'inactive'
        ]);

        $this->userRepository->shouldReceive('findByEmail')
            ->with('test@example.com')
            ->once()
            ->andReturn($user);

        $this->userRepository->shouldReceive('activate')
            ->with($user)
            ->once()
            ->andReturn(true);

        $result = $this->authService->verifyCode('test@example.com', '123456');

        $this->assertTrue($result);
    }

    public function test_verify_code_failure()
    {
        $user = new User([
            'account' => 'test@example.com',
            'email' => 'test@example.com',
            'veri_code' => '123456',
            'veri_expire' => now()->subMinutes(5),
            'status' => 'inactive'
        ]);

        $this->userRepository->shouldReceive('findByEmail')
            ->with('test@example.com')
            ->once()
            ->andReturn($user);

        $result = $this->authService->verifyCode('test@example.com', '123456');

        $this->assertFalse($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}