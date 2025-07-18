<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Models\purchasedModel as Order;
use App\Models\productsModel as Product;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        /* the Cloudflare certification conducts only in real web */
        if (config('app.url') === 'https://desmoco.com.tw') {
            if (!$this->validateTurnstile($request->input('cf-turnstile-response'))) {
                return back()->withErrors(['msg' => '請完成真人驗證']);
            }
        }

        try {
            // 先判別是否是管理員註冊
            $prvilige = str_starts_with($request->account, "admin./") ? "A" : "B";
            // 如果前綴是 admin./ 要幫他拿掉
            if ($prvilige == "A") {
                $head_away = explode("admin./", $request->account);
                $request->account = $head_away[1];
            }

            $veri_code = strval(rand(100000, 999999));  // 生成驗證碼
            // 寫入資料庫
            $user = User::create(
                [
                    'name' => $request->name,
                    'account' => $request->account,
                    'email' => $request->account,
                    'password' => Hash::make($request->password),
                    'prvilige' => $prvilige,
                    'status' => 'inactive',  // 預設狀態為 inactive
                    'veri_code' => $veri_code,
                    'veri_expire' => now()->addMinutes(7),
                ]
            );
            $user->email = $request->account;

            // 發送驗證郵件
            $to = $request->account;
            /* 發送信件，in vivo content */
            Mail::raw('感謝您註冊本站帳號，您的驗證碼為：' . $veri_code . '；請在7分鐘內回到網站進行驗證。', function ($message) use ($to) {
                $message->to($to)
                    ->subject('Shopit 註冊驗證信');
            });

            return view('auth.verification', compact('user'));
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

        /* the Cloudflare certification conducts only in real web */
        if (config('app.url') === 'https://desmoco.com.tw') {
            if (!$this->validateTurnstile($request->input('cf-turnstile-response'))) {
                return back()->withErrors(['msg' => '請完成真人驗證']);
            }
        }

        try {
            $credentials = $request->validate([
                'account' => 'required|email',
                'password' => 'required'
            ]);

            $user = User::where('email', $credentials['account'])->first();

            if (!$user) {
                return back()->withErrors(['msg' => '此帳號不存在']);
            }

            if (!Hash::check($credentials['password'], $user->password)) {
                return back()->withErrors(['msg' => '密碼錯誤']);
            }

            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended('home');
            }

            throw ValidationException::withMessages([
                'account' => ['登入失敗，請檢查您的帳號和密碼是否正確']
            ]);
        } catch (\Exception $e) {

            return back()->withErrors(['msg' => '系統錯誤，請稍後再試：' . $e->getMessage()]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    public function verification()
    {
        // 如果輸錯或過期甚麼的會由route回到這，就得重新傳遞user mail不然就要報錯
        $user = new \stdClass(); // 或者使用一個空的物件
        $user->email = session('user');

        return view('auth.verification', compact('user'));
    }

    public function verification_check(Request $request)
    {
        // dd($request->all());
        $verification_code = $request->verification_code;

        try {
            // 驗證碼檢查
            $user = User::where('email', $request->email)->first();

            // 驗證
            if ($verification_code != $user->veri_code) {
                return redirect()->route('verification')->withErrors(['msg' => '驗證碼錯誤，請重新輸入'])->with('user', $request->email);
            }
            if (now() > $user->veri_expire) {
                return redirect()->route('verification')->withErrors(['msg' => '驗證碼過期，請點擊重新寄送'])->with('user', $request->email);
            }

            // 如果驗證碼正確，則更新使用者狀態為 active
            $user->status = 'active';
            $user->save();

            // 如果使用者狀態 active，則登入使用者
            if ($user->status == 'active') {
                // 登入使用者 (檢查是否勾選暫存)
                $request->remember_me ? Auth::login($user, true) : Auth::login($user, false);

                // 看是不是管理員做不同的歡迎語
                if ($user->prvilige == "A") {
                    return redirect()->route('home')->with('success', '管理員' . $user->name . '註冊成功，您有權限進入訂單及編輯系統。');
                } else {
                    return redirect()->route('home')->with('success', '恭喜' . $user->name . '！註冊成功！');
                }
            } else {
                // 通過但卻沒有改active
                return redirect()->route('register')->withErrors(['msg' => '系統錯誤，請聯繫管理員']);
            }
        } catch (\Exception $e) {
            // 捕捉例外並返回錯誤訊息
            return redirect()->route('verification')->withErrors(['msg' => '系統錯誤，請稍後再試：' . $e->getMessage()]);
        }
    }

    public function verification_resend(Request $request)
    {

        $user = User::where('email', $request->email)->first();

        // 如果找到路由來到這
        if (!$user) {
            return back()->withErrors(['msg' => '未知來源，請重新註冊']);
        }

        // 生成新的驗證碼
        $veri_code = strval(rand(100000, 999999));  // 生成驗證碼
        // 寫入資料庫
        $user->veri_code = $veri_code;
        $user->veri_expire = now()->addMinutes(7);
        $user->save();


        // 重新發送驗證郵件
        $to = $request->email;
        Mail::raw('重新發送驗證碼，您的驗證碼為：' . $veri_code . '；請在7分鐘內回到網站進行驗證。
        <\br> 注意，若您多次重新發送仍無法成功登入，請聯絡系統管理員', function ($message) use ($to) {
            $message->to($to)
                ->subject('Shopit 註冊驗證信');
        });
        return redirect()->route('verification')->with('user', $request->email);
    }

    private function validateTurnstile($token)
    {
        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => env('CLOUDFLARE_TURNSTILE_SECRET_KEY'),
            'response' => $token,
        ]);

        return $response->json()['success'] ?? false;
    }

    public function verification_to_admin(Request $request) {}

    public function member_edit(Request $request)
    {
        $user = User::find(Auth::id());
        return view('auth.member_edit', compact('user'));
    }

    public function member_edit_save(Request $request)
    {
        // 獲取當前用戶
        $user = User::find(Auth::id());

        // 更新用戶資料
        try {
            $user->name = $request->input('name');
            $user->nickname = $request->input('nickname');
            $user->phone = $request->input('phone');
            $user->to_address = $request->input('address');
            $user->email = $request->input('email');

            // If a new password is provided, update it
            if ($request->filled('password')) {
                $user->password = Hash::make($request->input('password'));
            }

            // Save changes
            $user->save();

            return redirect()->route('member_edit')->with('success', '會員資料已更新成功！');
        } catch (\Exception $e) {
            // return redirect()->route('member_edit')->withErrors(['msg' => '更新失敗：' . $e->getMessage()]);
            return redirect()->route('member_edit')->withErrors(['msg' => '更新失敗']);
        }

        return redirect()->route('member_edit')->with('success', '會員資料已更新成功！');
    }

    public function order_query(Request $request)
    {
        $order_query = $request->input('order_query');
        $user = User::find(Auth::id());

        // 無輸入單號:搜尋全部
        if (empty($order_query)) {
            $orders = Order::where('account', $user->account)->get();
        } else {
            // 有輸入單號:搜尋單一
            $orders = Order::where('account', $user->account)->where('id', $order_query)->get();
        }
        if (empty($orders)) {
            return response()->json(['error' => '沒有找到相關訂單'], 404);
        }

        // 整理訂單資料 (找到訂單)
        foreach ($orders as $order) {
            $purchaseds = $order->purchased;
            if (!$purchaseds) {
                $order->purchased = [];
                continue;
            }

            $purchaseds = explode(';', $purchaseds);
            $ordered_purchaseds = [];
            foreach ($purchaseds as $purchased) {
                $purchased = explode(',', $purchased);
                if (count($purchased) < 3) {
                    continue; // 跳過格式不正確的資料
                }
                $product = Product::where('id', $purchased[0])->first();
                if (!$product) {
                    continue; // 跳過找不到商品的資料
                }
                $ordered_purchaseds[] = [
                    'product_name' => $product->product_name,
                    'number' => $purchased[1],
                    'price' => $purchased[2]
                ];
            }
            $order->purchased = $ordered_purchaseds;
        }

        return response()->json(['orders' => $orders]);
    }

    // 重定向到 Google 登入頁面
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Google 回調處理
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // 查詢是否已有該 Google 帳號的使用者
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // 如果使用者不存在，則創建新使用者
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => null,
                    'phone' => '0',
                ]);
            }

            // 登入該使用者
            Auth::login($user);

            // 重定向到首頁或其他頁面
            return redirect()->route('home');
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['msg' => 'Google 登入失敗，請稍後再試。']);
        }
    }
}
