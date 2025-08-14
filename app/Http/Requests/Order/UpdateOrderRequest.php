<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateOrderRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        $rules = [
            'product_id' => 'sometimes|required|exists:products,id',
            'store' => 'sometimes|required|string',
            'address' => 'sometimes|required|string|max:255',
            'name_input' => 'sometimes|required|string|max:255',
            'account_input' => 'sometimes|required|string|max:255',
        ];

        // 當確認付款時需要驗證所有必要欄位
        if ($this->routeIs('pay_confirm')) {
            $rules = array_merge($rules, [
                'name' => 'required|string',
                'bank_account' => 'required|string',
                'shop1_addr2' => 'required|in:1,2'
            ]);
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'product_id.required' => '商品ID為必填',
            'product_id.exists' => '商品不存在',
            'store.required' => '超商門市為必填',
            'address.required' => '寄送地址為必填',
            'address.max' => '地址長度不可超過255字元',
            'name_input.required' => '姓名為必填',
            'name_input.max' => '姓名長度不可超過255字元',
            'account_input.required' => '扣款帳號為必填',
            'account_input.max' => '帳號長度不可超過255字元',
            'name.required' => '取貨姓名為必填',
            'bank_account.required' => '扣款帳號為必填',
            'shop1_addr2.required' => '請選擇寄送方式',
            'shop1_addr2.in' => '寄送方式不正確'
        ];
    }
}