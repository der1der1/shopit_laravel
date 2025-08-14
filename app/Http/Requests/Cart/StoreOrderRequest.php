<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'selected_items' => 'required|array',
            'selected_items.*' => 'required|integer|exists:productsModel,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|integer|min:1'
        ];
    }

    public function messages()
    {
        return [
            'selected_items.required' => '請選擇商品',
            'selected_items.array' => '商品資料格式錯誤',
            'selected_items.*.exists' => '所選商品不存在',
            'quantity.required' => '請輸入數量',
            'quantity.array' => '數量資料格式錯誤',
            'quantity.*.required' => '請輸入所有商品數量',
            'quantity.*.min' => '商品數量不可為0'
        ];
    }
}