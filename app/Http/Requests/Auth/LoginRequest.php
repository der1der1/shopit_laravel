<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'account' => 'required|email',
            'password' => 'required',
            'remember' => 'boolean',
            'cf-turnstile-response' => config('app.url') === 'https://desmoco.com.tw' ? 'required|string' : 'nullable'
        ];
    }

    public function messages()
    {
        return [
            'account.required' => '請輸入帳號',
            'account.email' => '帳號必須是有效的電子郵件地址',
            'password.required' => '請輸入密碼',
            'cf-turnstile-response.required' => '請完成真人驗證'
        ];
    }
}