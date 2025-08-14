<?php

namespace App\Http\Requests\Contact;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'information' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '請輸入姓名',
            'name.max' => '姓名不可超過255個字元',
            'email.required' => '請輸入電子郵件',
            'email.email' => '請輸入有效的電子郵件地址',
            'email.max' => '電子郵件不可超過255個字元',
            'phone.required' => '請輸入電話號碼',
            'phone.max' => '電話號碼不可超過20個字元',
            'information.required' => '請輸入訊息內容'
        ];
    }
}