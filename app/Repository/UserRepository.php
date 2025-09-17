<?php

namespace App\Repository;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserRepository
{

    public function createUserDB($request, $prvilige, $veri_code)
    {
        // 寫入資料庫
        $user = User::create(
            [
                'name' => $request->name,
                'account' => $request->account,
                'email' => $request->account,
                'password' => Hash::make($request->password),
                'prvilige' => $prvilige,
                'status' => 'inactive',  // 預設狀態為 inactive
                'veri_code' => $veri_code,
                'veri_expire' => now()->addMinutes(7),
            ]
        );
    }

    public function findUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function updateUser($user, $request)
    {
        // Update user data
        $user->name = $request->input('name');
        $user->nickname = $request->input('nickname');
        $user->phone = $request->input('phone');
        $user->to_address = $request->input('address');
        $user->email = $request->input('email');

        // If a new password is provided, update it
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // Save changes
        $user->save();
        
        return $user;
    }

}
