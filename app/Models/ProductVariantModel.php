<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantModel extends Model
{
    use HasFactory;
    
    protected $table = 'product_variants';

    protected $fillable = [
        'product_id',
        'variant_name',
        'unicode',
        'price',
        'ori_price',
        'use_oriprice',
        'quantity',
        'min_quantity',
        'pic_dir',
        'is_default',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'ori_price' => 'decimal:2',
        'use_oriprice' => 'boolean',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'quantity' => 'integer',
        'min_quantity' => 'integer',
        'sort_order' => 'integer'
    ];

    /**
     * 關聯到商品
     */
    public function product()
    {
        return $this->belongsTo(productsModel::class, 'product_id');
    }

    /**
     * 檢查是否缺貨
     */
    public function isOutOfStock(): bool
    {
        return $this->quantity <= 0;
    }

    /**
     * 檢查是否低於最低庫存
     */
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->min_quantity && $this->quantity > 0;
    }

    /**
     * 取得顯示價格
     */
    public function getDisplayPrice(): float
    {
        return $this->use_oriprice && $this->ori_price ? $this->ori_price : $this->price;
    }

    /**
     * 扣除庫存
     */
    public function decrementStock(int $quantity): bool
    {
        if ($this->quantity < $quantity) {
            return false;
        }
        
        $this->decrement('quantity', $quantity);
        return true;
    }

    /**
     * 增加庫存
     */
    public function incrementStock(int $quantity): void
    {
        $this->increment('quantity', $quantity);
    }
}
