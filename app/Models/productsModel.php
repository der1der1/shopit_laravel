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
        'pic_name_more',
        'pic_dir_more',
        'description',
        'price',
        'ori_price',
        'category',
        'selected',
        'is_active',
        'quantity',
        'min_quantity',
        'pay_methods'
    ];

    /**
     * 關聯到商品品項
     */
    public function variants()
    {
        return $this->hasMany(ProductVariantModel::class, 'product_id');
    }
}

