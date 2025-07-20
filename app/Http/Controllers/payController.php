<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\marqeeModel;
use App\Models\purchasedModel;
use App\Models\productsModel;
use app\Models\User;




class payController extends Controller
{
    public function pay_show() {
        $user = Auth::user();
        $marqee = marqeeModel::getAllMarqee();

        $ppl_info = User::where('account', $user->account)->first();
        $purchased = purchasedModel::where('account', $user->account)->orderBy('id', 'desc')->first();


        $purchasies = explode(';', $purchased->purchased);

        $products = [];
        foreach ($purchasies as $purchase) {
            $purchase = explode(',', $purchase);
            $purchase_product = productsModel::where('id', $purchase[0])->first();
            // 把user table 和 product table的東西整合在一個陣列中，傳遞前端僅需傳遞此陣列。
            $products[] = [
                'id' => $purchase[0],
                'num' => $purchase[1],
                'pic_dir' => $purchase_product->pic_dir,
                'product_name' => $purchase_product->product_name,
                'description' => $purchase_product->description,
                'price' => $purchase_product->price,
            ];

        }

        return view('pay', compact('user', 'marqee', 'products', 'ppl_info', 'purchased' ));
    }

    public function map() {
        return view('map');
    }

}
