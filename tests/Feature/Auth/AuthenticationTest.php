<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_user_can_view_registration_form()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    public function test_user_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'account' => 'test@example.com',
            'password' => 'password123',
            'phone' => '0987654321'
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('auth.verification');
        
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'status' => 'inactive'
        ]);
    }

    public function test_user_can_view_login_form()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_user_can_login_with_correct_credentials()
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

        $response = $this->post('/login', [
            'account' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_incorrect_credentials()
    {
        User::create([
            'name' => 'Test User',
            'account' => 'test@example.com',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
            'phone' => '0987654321',
            'prvilige' => 'B'
        ]);

        $response = $this->post('/login', [
            'account' => 'test@example.com',
            'password' => 'wrong_password'
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_user_can_logout()
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

        $this->actingAs($user);
        
        $response = $this->post('/logout');
        
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_verification_code_validation()
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
            'veri_expire' => now()->addMinutes(7)
        ]);

        $response = $this->post('/verification/check', [
            'email' => 'test@example.com',
            'verification_code' => '123456'
        ]);

        $response->assertRedirect('/home');
        
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'status' => 'active'
        ]);
    }

    public function test_verification_code_resend()
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
            'veri_expire' => now()->addMinutes(7)
        ]);

        $response = $this->post('/verification/resend', [
            'email' => 'test@example.com'
        ]);

        $response->assertRedirect('/verification');
        Mail::assertSent(function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_user_can_update_profile()
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

        $this->actingAs($user);

        $response = $this->post('/member/edit', [
            'name' => 'Updated Name',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'address' => 'Test Address'
        ]);

        $response->assertRedirect('/member/edit');
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'phone' => '1234567890',
            'to_address' => 'Test Address'
        ]);
    }
}