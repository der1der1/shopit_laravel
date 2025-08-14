<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class ListOrderRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->prvilige === 'A';
    }

    public function rules()
    {
        return [
            'id_done' => 'required|exists:purchaseds,id',
            'account_done' => 'required|exists:users,account'
        ];
    }

    public function messages()
    {
        return [
            'id_done.required' => '訂單編號為必填',
            'id_done.exists' => '找不到該訂單',
            'account_done.required' => '用戶帳號為必填',
            'account_done.exists' => '找不到該用戶'
        ];
    }
}