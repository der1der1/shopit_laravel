<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class productsModel extends Authenticatable
{
    use HasFactory;
    
    protected $table = 'products';

    // 篩選的邏輯應該寫在controller中

    protected $fillable = [
        'pic_name',
        'product_name', 
        'pic_dir',
        'description',
        'price',
        'ori_price',
        'category'
    ];
}

