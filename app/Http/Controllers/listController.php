<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\marqeeModel;
use Illuminate\Http\Request;
use App\Models\purchasedModel;
use App\Models\productsModel;
use App\Models\User;




class listController extends Controller
{
    public function list_show() {
        $user = Auth::user();
        // 權限A可以進入
        if ( $user->prvilige == "A" ) {
            $marqee = marqeeModel::getAllMarqee();
            $lists = purchasedModel::where('show', "1")->get();
            
            $new_lists = array();

            
            // 此階層是每人的單
            foreach ($lists as $list) {
                $single_products = explode(';', $list->purchased);

                // 取出購買物的id去搜尋其產品訊息
                $purchased_products = array();

                // 此階層是每人的每個物件，即建立巢狀陣列
                foreach ($single_products as $single_product) {
                    $single_product = explode(',', $single_product);
                    $purchased_product = productsModel::where('id', $single_product[0])->first();
                    $purchased_products[] = [
                        'id' => $purchased_product->id,
                        'product_name' => $purchased_product->product_name,
                        'price' => $purchased_product->price,
                        'num' => $single_product[1],
                    ];
                }
                // product為巢狀陣列
                $new_lists[] = [
                    'id' =>$list->id,
                    'account' => $list->account,
                    'name' => $list->name,
                    'to_shop' => $list->to_shop,
                    'to_address' => $list->to_address,
                    'shop1_addr2' => $list->shop1_addr2,
                    'product' => $purchased_products,
                ];
            }
            return view('list', compact('user', 'marqee', 'new_lists' ));
        } else {
            // 權限B級則拒絕並返回首頁
            return redirect()->route('home')->with('error', '您沒有權限執行此操作');
        }
        
    }

    public function list_store(Request $request) {
        $user = Auth::user()->account;

        // 在他的purchased table 的pay、delivered、show欄位寫入
        $purchased = purchasedModel::where('id', $request->id_done)->first();
        $purchased->payed = "1";
        $purchased->delivered = "1";
        $purchased->show = "0";
        $purchased->save();

        // 在他的user table 更新通知訊息
        $info_ori = User::where('account', $request->account_done)->first();
        $info_new = "訂購商品已寄出！" . date("Y/m/d H:i:s") . ';' . $info_ori->info0; 
        // 維持三則訊息的處理
        $info_num = explode(';', $info_new);
        if (count($info_num) > 3 ) {
            $info_new = $info_num[0] .';'. $info_num[1] .';'. $info_num[2];
        }
        // 在他的通知訊息欄位寫入
        $info_ori->info0 = $info_new;
        $info_ori->save();

        return redirect()->route('list_show')->with('success', '儲存成功');
    }
}
