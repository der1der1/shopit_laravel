<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class AddProductRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->prvilige === 'A';
    }

    public function rules()
    {
        return [
            'pic_name' => 'required|string|max:255',
            'product_name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'ori_price' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'pic_dir' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }

    public function messages()
    {
        return [
            'pic_name.required' => '圖片名稱為必填',
            'product_name.required' => '商品名稱為必填',
            'description.required' => '商品描述為必填',
            'price.required' => '價格為必填',
            'price.numeric' => '價格必須為數字',
            'price.min' => '價格不能為負數',
            'ori_price.required' => '原價為必填',
            'ori_price.numeric' => '原價必須為數字',
            'ori_price.min' => '原價不能為負數',
            'category.required' => '類別為必填',
            'pic_dir.required' => '必須上傳圖片',
            'pic_dir.image' => '必須上傳圖片檔案',
            'pic_dir.mimes' => '圖片格式必須為 jpeg, png, jpg 或 gif',
            'pic_dir.max' => '圖片大小不能超過 2MB'
        ];
    }
}