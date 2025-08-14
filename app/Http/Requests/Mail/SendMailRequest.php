<?php

namespace App\Http\Requests\Mail;

use Illuminate\Foundation\Http\FormRequest;

class SendMailRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
            'products' => 'required_if:type,purchase_confirmation|array',
            'products.*.id' => 'required_if:type,purchase_confirmation|integer',
            'products.*.name' => 'required_if:type,purchase_confirmation|string',
            'purchased' => 'required_if:type,purchase_confirmation|array',
            'purchased.id' => 'required_if:type,purchase_confirmation|integer',
            'purchased.total' => 'required_if:type,purchase_confirmation|numeric',
            'type' => 'required|in:test,test_with_attachment,purchase_confirmation'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => '收件人信箱為必填',
            'email.email' => '收件人信箱格式不正確',
            'products.required_if' => '購買確認郵件需要商品資訊',
            'products.array' => '商品資訊格式不正確',
            'products.*.id.required_if' => '商品ID為必填',
            'products.*.id.integer' => '商品ID必須為整數',
            'products.*.name.required_if' => '商品名稱為必填',
            'products.*.name.string' => '商品名稱必須為字串',
            'purchased.required_if' => '購買確認郵件需要訂單資訊',
            'purchased.array' => '訂單資訊格式不正確',
            'purchased.id.required_if' => '訂單ID為必填',
            'purchased.id.integer' => '訂單ID必須為整數',
            'purchased.total.required_if' => '訂單總額為必填',
            'purchased.total.numeric' => '訂單總額必須為數字',
            'type.required' => '郵件類型為必填',
            'type.in' => '郵件類型不正確'
        ];
    }
}