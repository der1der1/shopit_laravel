<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
    }

    public function test_create_user()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'account' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 'inactive',
            'prvilige' => 'B'
        ];

        $user = $this->userRepository->create($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userData['name'], $user->name);
        $this->assertEquals($userData['email'], $user->email);
        $this->assertEquals($userData['status'], $user->status);
        $this->assertEquals($userData['prvilige'], $user->prvilige);
    }

    public function test_find_user_by_id()
    {
        $user = User::create([
            'name' => 'Test User',
            'account' => 'test@example.com',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
            'phone' => '0987654321',
            'prvilige' => 'B'
        ]);

        $foundUser = $this->userRepository->findById($user->id);

        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($user->id, $foundUser->id);
    }

    public function test_find_user_by_email()
    {
        $user = User::create([
            'name' => 'Test User',
            'account' => 'test@example.com',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
            'phone' => '0987654321',
            'prvilige' => 'B'
        ]);

        $foundUser = $this->userRepository->findByEmail('test@example.com');

        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($user->email, $foundUser->email);
    }

    public function test_update_user()
    {
        $user = User::create([
            'name' => 'Test User',
            'account' => 'test@example.com',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
            'phone' => '0987654321',
            'prvilige' => 'B'
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'phone' => '1234567890',
            'to_address' => 'Test Address'
        ];

        $success = $this->userRepository->update($user->id, $updateData);

        $this->assertTrue($success);
        
        $updatedUser = $this->userRepository->findById($user->id);
        $this->assertEquals($updateData['name'], $updatedUser->name);
        $this->assertEquals($updateData['phone'], $updatedUser->phone);
        $this->assertEquals($updateData['to_address'], $updatedUser->to_address);
    }

    public function test_activate_user()
    {
        $user = User::create([
            'name' => 'Test User',
            'account' => 'test@example.com',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 'inactive',
            'phone' => '0987654321',
            'prvilige' => 'B'
        ]);

        $success = $this->userRepository->activate($user);

        $this->assertTrue($success);
        
        $activatedUser = $this->userRepository->findById($user->id);
        $this->assertEquals('active', $activatedUser->status);
    }

    public function test_update_verification_code()
    {
        $user = User::create([
            'name' => 'Test User',
            'account' => 'test@example.com',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 'inactive',
            'phone' => '0987654321',
            'prvilige' => 'B',
            'veri_code' => '123456',
            'veri_expire' => now()
        ]);

        $newCode = '654321';
        $success = $this->userRepository->updateVerificationCode($user, $newCode);

        $this->assertTrue($success);
        
        $updatedUser = $this->userRepository->findById($user->id);
        $this->assertEquals($newCode, $updatedUser->veri_code);
        $this->assertTrue($updatedUser->veri_expire > now());
    }
}