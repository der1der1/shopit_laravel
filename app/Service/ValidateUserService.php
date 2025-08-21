<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class ValidateUserService
{

    public function ValidateInput(Request $request)
    {
        $credentials = $request->validate([
            'account' => 'required|email',
            'password' => 'required'
        ]);

        return $credentials;
    }

    public function ValidateUser ($user, $credentials, $request)
    {
        if (!$user) {
            return back()->withErrors(['msg' => '此帳號不存在']);
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['msg' => '密碼錯誤']);
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('home');
        }
    }

}
