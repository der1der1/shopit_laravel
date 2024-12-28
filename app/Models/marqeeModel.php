<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class marqeeModel extends Authenticatable
{
    use HasFactory;

    protected $table = 'marqee';

    // 可以添加更多的方法來處理查詢邏輯
    public static function getAllMarqee()
    { 
        return self::inRandomOrder()->get();
    }


}
