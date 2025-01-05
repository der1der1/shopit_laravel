<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\marqeeModel;
use app\Models\User;
use App\Models\productsModel;
use Illuminate\Http\Request;
use App\Models\purchasedModel;


class checkController extends Controller
{
    // 要先驗證是否已經登入
    public function check_show() {

        
        $user = Auth::user();
        $marqee = marqeeModel::getAllMarqee();
        $wanted_product = array(); // 原先在資料庫紀錄的是product id，現在要轉存成該商品的全列

        // 抓出使用者的want欄位
        $wanted_ids = User::where('account', Auth::user()->account)->pluck('want');
        // 刪除數字及逗點以外的一切 (正則表達式)
        $wanted_ids = preg_replace('/[^\d,]/', '', $wanted_ids);
        // 第一個字元如果是逗號則刪除該字元；isset($input[0])：確認變數 $input 的第一個字元是否存在；$input[0] === ','：檢查第一個字元是否為逗號。
        $wanted_ids = isset($wanted_ids[0]) && $wanted_ids[0] === ',' ? substr($wanted_ids, 1) : $wanted_ids;

        // 把該欄位的字串改成陣列
        $wanted_ids_array = explode(",", $wanted_ids);
        // 對陣列內的每個id做搜尋並逐筆寫入id的產品資訊
        foreach ($wanted_ids_array as $wanted_ids_arrays) {
            $wanted_product[] = productsModel::where('id', $wanted_ids_arrays)->first();
        }
        $wanted_product = array_values(array_unique($wanted_product));

        return view('check', compact('user', 'marqee', 'wanted_product'));
    }
    public function check_store(Request $request) {
        
        $user = Auth::user();

        // 處裡要購買的項目
        // 取得物件id
        $itemIds = $request->input('selected_items', []);
        // if user seleced nothing
        if (empty($itemIds)) {
            return redirect()->route('check_show')->with('error', '請選擇商品');
        }
        // 取得數量
        $quantity = $request->input('quantity', []);
        // 如果有商品數量為0則報錯返回
        foreach ($quantity as $quantitys) {
            if ($quantitys == 0) {
                return redirect()->route('check_show')->with('error', '選取的商品數量不可為 0 喔!');
            }
        }
        // 取得數量，並過濾空值
        $quantity = array_filter($quantity);
        // 取得價格
        $price = array();
        foreach ($itemIds as $itemId) {
            $price[] = productsModel::where('id', $itemId)->pluck('price')->first();
        }
        // 使用 array_map 並合併三個陣列
        $merged_arr = array_map(function($v1, $v2, $v3) {
            return $v1 . ',' . $v2 . ',' . $v3;
        }, $itemIds, $quantity, $price);
        // 陣列轉字串
        $purchased = implode(';', $merged_arr);

        // 備註：儲存已購買的陣列紀錄 [ 商品1,數量,價格 ; 商品2,數量,價格 ; ...]

        // 計算總價
        $price_total = 0;
        foreach ($merged_arr as $merged_arrs) {
            $items = explode(',', $merged_arrs);
            $price_total = $price_total + $items[1] * $items[2];
        }



        // 刪除user table `want` column內已經購買的項目
        // 取得原先陣列
        $wanted_ids = User::where('account', Auth::user()->account)->pluck('want');
        $wanted_ids = preg_replace('/[^\d,]/', '', $wanted_ids);
        $wanted_ids_array = explode(",", $wanted_ids);
        // 原先陣列要單一值也要刪除空值
        $wanted_ids_array = array_filter(array_unique($wanted_ids_array));
        // 刪除原先陣列與itemIds中相同的元素
        $new_wanted_ids_arr = array_diff($wanted_ids_array, $itemIds);
        // 將結果轉回字串
        $new_wanted_ids = implode(",", $new_wanted_ids_arr);


        try {
            $auth = Auth::user()->account;
        // 1. 更新user的want欄位
            // 依據登入的使用找找到他的資料
            $user = User::where('account', $auth)->first();
            // 在他的want欄位寫入
            $user->want = $new_wanted_ids . ',';
            $user->save();

        // 2. 新增purchase清單
            $purchase = purchasedModel::create([
                'account' => $auth,
                'purchased' => $purchased,
                'bill' => $price_total,
                'payed' => "0",
                'delivered' => "0",
                'recieved' => "0",
                'show' => "0",
            ]);


        // return view('home', compact('user', 'marqee', 'wanted_product'));
        return redirect()->route('pay_show')->with('success', '儲存成功');

        // return redirect()->route('home')->with('success', '送出成功');
        } catch (\Exception $e) {

            return back()->withErrors(['msg' => $e->getMessage()]);
        }
        
    }
    
}
