<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\User as Authenticatable;
use app\Models\User;
use App\Models\purchasedModel;
use Illuminate\Http\Request;
use App\Models\marqeeModel;
use Log;


class purchasedCtlr extends Controller
{
        // 存入購物車
    public function want(Request $request) {
        $auth = Auth::user()->account;

        // 依據登入的使用找找到他的資料
        $user = User::where('account', $auth)->first();

        // 先取出已經存在內的資料
        $before_want = $user->want;
        // 加入本次要寫入的資料，加入逗號，之後如果要做處裡可作分割用
        $after_want = $before_want . $request->product_id . ','; 
        // 在他的want欄位寫入
        $user->want = $after_want;

        $user->save();

        return redirect()->route('home')->with('success', '加入成功');

    }

    public function pay_to_shop(Request $request) {
        $user = Auth::user()->account;
        
        // 在他的to_shop欄位寫入
        $purchased = purchasedModel::where('account', $user)->orderBy('id', 'desc')->first();
        $user = User::where('account', $user)->first();

        // purchase 和 user table的 欄位都要更新
        $purchased->to_shop = $request->store;
        $user->to_shop = $request->store;
        $purchased->shop1_addr2 = "1";

        $purchased->save();
        $user->save();

        return redirect()->route('pay_show')->with('success', '超商寄送到'.$request->store);

    }
    public function pay_to_home(Request $request) {
        if (empty($request->address)) {
            return redirect()->route('pay_show')->with('error', '住家地址不可空白');
        }
        $user = Auth::user()->account;

        // 在他的to_address欄位寫入
        $purchased = purchasedModel::where('account', $user)->orderBy('id', 'desc')->first();
        $user = User::where('account', $user)->first();

        $purchased->to_address = $request->address;
        $user->to_address = $request->address;
        $purchased->shop1_addr2 = "2";

        $purchased->save();
        $user->save();

        return redirect()->route('pay_show')->with('success', '宅配到：'.$request->address);
    }

    public function pay_name(Request $request) {
        if (empty($request->name_input)) {
            return redirect()->route('pay_show')->with('error', '請輸入姓名');
        }
        $user = Auth::user()->account;

        // 在他的user table 的name欄位寫入
        $user_model = User::where('account', $user)->first();
        $user_model->name = $request->name_input;
        $user_model->save();

        // 在他的purchased table 的name欄位寫入
        $purchased = purchasedModel::where('account', $user)->orderBy('id', 'desc')->first();
        $purchased->name = $request->name_input;
        $purchased->save();

        return redirect()->route('pay_show')->with('success', '取貨大名：'.$request->name_input);
    }

    public function pay_account(Request $request) {
        if (empty($request->account_input)) {
            return redirect()->route('pay_show')->with('error', '請輸入正確的扣款帳號');
        }
        $user = Auth::user()->account;

        // 在他的bank_account欄位寫入
        $purchased = purchasedModel::where('account', $user)->orderBy('id', 'desc')->first();
        $user = User::where('account', $user)->first();

        $purchased->bank_account = $request->account_input;
        $user->bank_account = $request->account_input;

        $purchased->save();
        $user->save();

        return redirect()->route('pay_show')->with('success', '扣款帳號：'.$request->account_input);
    }
    public function pay_confirm(Request $request) {
        // 防禦性，所有欄位檢查不可空白
        if (empty($request->name)||empty($request->bank_account)||empty($request->shop1_addr2)) {
            return redirect()->route('pay_show')->with('error', '資料未填寫完整');
        }
        $user = Auth::user()->account;
        $purchased = purchasedModel::where('account', $user)->orderBy('id', 'desc')->first();

        // 更新顯示在訂購清單上 show = 1
        $purchased->show = "1";
        $purchased->save();

        // 更新通知訊息
        // 依據登入的使用找找到他的資料，先取出已經存在內的資料
        $info_ori = User::where('account', $user)->first();


        // 加入本次要寫入的資料，加入逗號，之後如果要做處裡可作分割用
        $info_new = "您的訂單已送出！" . date("Y/m/d H:i:s") . ';' . $info_ori->info0; 

        // 維持三則訊息的處理
        $info_num = explode(';', $info_new);
        if (count($info_num) > 3 ) {
            $info_new = $info_num[0] .';'. $info_num[1] .';'. $info_num[2];
        }

        // 在他的want欄位寫入
        $info_ori->info0 = $info_new;
        $info_ori->save();

        return redirect()->route('home')->with('success', '購買成功，訂單id：'.$purchased->id);
    }
}
