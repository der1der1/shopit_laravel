<?php

namespace App\Services;

use App\Models\User;
use App\Services\EmailService;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected $emailService;
    protected $userRepository;

    public function __construct(EmailService $emailService, UserRepository $userRepository)
    {
        $this->emailService = $emailService;
        $this->userRepository = $userRepository;
    }

    public function register(array $data)
    {
        // Check if admin registration
        $privilege = str_starts_with($data['account'], "admin./") ? "A" : "B";
        
        // Remove admin prefix if present
        if ($privilege == "A") {
            $account = explode("admin./", $data['account'])[1];
        } else {
            $account = $data['account'];
        }

        $verificationCode = strval(rand(100000, 999999));
        
        $userData = [
            'name' => $data['name'],
            'account' => $account,
            'email' => $account,
            'password' => Hash::make($data['password']),
            'prvilige' => $privilege,
            'status' => 'inactive',
            'veri_code' => $verificationCode,
            'veri_expire' => now()->addMinutes(7),
        ];

        $user = $this->userRepository->create($userData);
        
        // Send verification email
        $this->emailService->sendVerificationEmail($user->email, $verificationCode);

        return $user;
    }

    public function authenticate(array $credentials, bool $remember = false)
    {
        $user = $this->userRepository->findByEmail($credentials['account']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return false;
        }

        Auth::login($user, $remember);
        return true;
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }

    public function verifyCode(string $email, string $code)
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || $code != $user->veri_code || now() > $user->veri_expire) {
            return false;
        }

        $this->userRepository->activate($user);
        return true;
    }

    public function resendVerificationCode(string $email)
    {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user) {
            return false;
        }

        $verificationCode = strval(rand(100000, 999999));
        
        $this->userRepository->updateVerificationCode($user, $verificationCode);
        $this->emailService->sendVerificationEmail($email, $verificationCode, true);

        return true;
    }

    public function handleGoogleAuth()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = $this->userRepository->findByEmail($googleUser->getEmail());

            if (!$user) {
                $user = $this->userRepository->create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => null,
                    'phone' => '0',
                    'status' => 'active'
                ]);
            }

            Auth::login($user);
            return $user;
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'email' => ['Google 登入失敗，請稍後再試。']
            ]);
        }
    }

    public function updateMember(int $userId, array $data)
    {
        return $this->userRepository->update($userId, $data);
    }
}