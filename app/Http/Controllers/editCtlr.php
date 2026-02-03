<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\marqeeModel;
use App\Models\productsModel;


use Illuminate\Http\Request;

class editCtlr extends Controller
{
    // 使之可以在本controller內共用
    private $products;

    public function edit_show() {
        $user = Auth::user();
        // 權限A可以進入
        if ( $user->prvilige == "A" ) {
            $marqee = marqeeModel::getAllMarqee();
            $products = productsModel::all();
            return view('edit', compact('user', 'marqee', 'products' ));
        } else {
            // 權限B級則拒絕並返回首頁
            return redirect()->route('home')->with('error', '您沒有權限執行此操作');
        }
    }


    public function edit_product_store(Request $request) {

        $product = productsModel::where('id', $request->id)->first();

        // 如果要刪除先刪掉
        if ($request->delete == "1") {
            $product->delete();
        } else {
            // 直接更新資料（Laravel ORM 會自動偵測變更）
            $product->fill($request->only([
                'id',
                'pic_name',
                'product_name',
                'description',
                'price',
                'ori_price',
                'category'
            ]));
            $product->save();
        }
 
        $request->pic_name;
        $request->product_name;
        $request->description;
        $request->price;
        $request->ori_price;
        $request->category;
        $request->delete;

        // 5. 圖片處理區
        // 檢查是否有上傳檔案
        // 找到產品
        // 檢查是否有上傳新圖片
        if ($request->hasFile('pic_dir')) {
            // 取得上傳的圖片
            $image = $request->file('pic_dir');

            // 移動新圖片到目標位置
            $image->move(public_path('img/pictureTarget'), $image->getClientOriginalName());
            
            // 更新資料庫圖片路徑
            $product->pic_dir = 'img/pictureTarget/' . $image->getClientOriginalName();
            $product->save();
        }
        return redirect()->route('edit_show');
    }

    public function edit_product_add(Request $request) {
        try {
            // 取得上傳的圖片
            $image = $request->file('pic_dir');
            // 移動新圖片到目標位置
            $image->move(public_path('img/pictureTarget'), $image);


            // $new_product = productsModel::create([
            productsModel::create([
                'pic_name' => $request->pic_name,
                'product_name' => $request->product_name,
                'pic_dir' => 'img/pictureTarget/' . $image->getClientOriginalName(),
                'description' => $request->description,
                'price' => $request->price,
                'ori_price' => $request->ori_price,
                'category' => $request->category,
            ]);
            // $new_product->save();

            return redirect()->route('edit_show')->with('success', '新增成功');
        } catch (\Exception $e) {

            return back()->withErrors(['msg' => $e->getMessage()]);
        }
    }



}
