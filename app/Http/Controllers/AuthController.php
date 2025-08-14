<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\MemberUpdateRequest;
use App\Services\AuthService;
use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    protected $authService;
    protected $orderRepository;
    protected $userRepository;

    public function __construct(
        AuthService $authService, 
        OrderRepository $orderRepository,
        UserRepository $userRepository
    ) {
        $this->authService = $authService;
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        try {
            $user = $this->authService->register($request->validated());
            return view('auth.verification', compact('user'));
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => $e->getMessage()]);
        }
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function authenticate(LoginRequest $request)
    {
        try {
            if ($this->authService->authenticate($request->validated(), $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended('home');
            }

            return back()->withErrors(['msg' => '登入失敗，請檢查您的帳號和密碼是否正確']);
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => '系統錯誤，請稍後再試：' . $e->getMessage()]);
        }
    }

    public function logout(Request $request)
    {
        $this->authService->logout();
        return redirect()->route('home');
    }

    public function verification()
    {
        $user = new \stdClass();
        $user->email = session('user');
        return view('auth.verification', compact('user'));
    }

    public function verification_check(Request $request)
    {
        try {
            $success = $this->authService->verifyCode($request->email, $request->verification_code);
            
            if (!$success) {
                return redirect()->route('verification')
                    ->withErrors(['msg' => '驗證碼錯誤或已過期'])
                    ->with('user', $request->email);
            }

            // Login user
            $user = $this->userRepository->findByEmail($request->email);
            $request->remember_me ? Auth::login($user, true) : Auth::login($user);

            $message = $user->prvilige == "A" 
                ? '管理員' . $user->name . '註冊成功，您有權限進入訂單及編輯系統。'
                : '恭喜' . $user->name . '！註冊成功！';

            return redirect()->route('home')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('verification')
                ->withErrors(['msg' => '系統錯誤，請稍後再試：' . $e->getMessage()]);
        }
    }

    public function verification_resend(Request $request)
    {
        try {
            $success = $this->authService->resendVerificationCode($request->email);

            if (!$success) {
                return back()->withErrors(['msg' => '未知來源，請重新註冊']);
            }

            return redirect()->route('verification')->with('user', $request->email);
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => '系統錯誤，請稍後再試：' . $e->getMessage()]);
        }
    }

    public function member_edit()
    {
        $user = $this->userRepository->findById(Auth::id());
        return view('auth.member_edit', compact('user'));
    }

    public function member_edit_save(MemberUpdateRequest $request)
    {
        try {
            $success = $this->authService->updateMember(Auth::id(), $request->validated());

            if (!$success) {
                return redirect()->route('member_edit')->withErrors(['msg' => '更新失敗']);
            }

            return redirect()->route('member_edit')->with('success', '會員資料已更新成功！');
        } catch (\Exception $e) {
            return redirect()->route('member_edit')->withErrors(['msg' => '更新失敗']);
        }
    }

    public function order_query(Request $request)
    {
        try {
            $user = $this->userRepository->findById(Auth::id());
            $orders = $this->orderRepository->findByAccount($user->account, $request->input('order_query'));

            if ($orders->isEmpty()) {
                return response()->json(['error' => '沒有找到相關訂單'], 404);
            }

            return response()->json(['orders' => $orders]);
        } catch (\Exception $e) {
            return response()->json(['error' => '查詢失敗'], 500);
        }
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = $this->authService->handleGoogleAuth();
            return redirect()->route('home');
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['msg' => 'Google 登入失敗，請稍後再試。']);
        }
    }
}
