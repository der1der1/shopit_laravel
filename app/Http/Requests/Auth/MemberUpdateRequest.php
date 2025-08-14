<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MemberUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'password' => 'nullable|string|min:6|max:255'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '請輸入姓名',
            'email.required' => '請輸入電子郵件',
            'email.email' => '請輸入有效的電子郵件地址',
            'email.unique' => '此電子郵件已被使用',
            'password.min' => '密碼至少需要6個字元'
        ];
    }
}