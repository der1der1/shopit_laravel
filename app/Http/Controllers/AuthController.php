<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        try {
            // 先判別是否是管理員註冊
            $prvilige = str_starts_with($request->account, "admin./") ? "A" : "B";
            // 如果前綴是 admin./ 要幫他拿掉
            if ($prvilige == "A") {
                $head_away = explode("admin./", $request->account);
                $request->account = $head_away[1];
            }

            // 寫入資料庫
            $user = User::create(
                [
                    'name' => $request->name,
                    'account' => $request->account,
                    'password' => Hash::make($request->password),
                    'prvilige' => $prvilige,
                ]
            );
            Auth::login($user);

            // 看是不是管理員做不同的歡迎語
            if ($prvilige == "A") {
                return redirect()->route('home')->with('success', '管理員'.$request->name.'註冊成功，您有權限進入訂單及編輯系統。');
            } else {
                return redirect()->route('home')->with('success', '恭喜'.$request->name.'！註冊成功！');
            }
        } catch (\Exception $e) {

            return back()->withErrors(['msg' => $e->getMessage()]);
        }
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'account' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('home');
        }

        throw ValidationException::withMessages([
            'account' => [trans('auth.failed')]
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}