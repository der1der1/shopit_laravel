<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;



class CreateUserService
{

    public function classifyUserType(Request $request)
    {
        // 判別是否是管理員註冊
        $prvilige = str_starts_with($request->account, "admin./") ? "A" : "B";
        
        return $prvilige;
    }

}
