<?php

namespace App\Repository;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class CreateUserRepository
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



    
}
