<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'account' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|max:255',
            'cf-turnstile-response' => config('app.url') === 'https://desmoco.com.tw' ? 'required|string' : 'nullable'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '請輸入姓名',
            'account.required' => '請輸入帳號',
            'account.email' => '帳號必須是有效的電子郵件地址',
            'account.unique' => '此帳號已被使用',
            'password.required' => '請輸入密碼',
            'password.min' => '密碼至少需要6個字元',
            'cf-turnstile-response.required' => '請完成真人驗證'
        ];
    }
}