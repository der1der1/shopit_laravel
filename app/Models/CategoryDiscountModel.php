<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryDiscountModel extends Model
{
    protected $table = 'category_discounts';

    protected $fillable = [
        'is_active',
        'discount_value',
        'categories',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'discount_value' => 'integer',
        'categories' => 'array',
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
    ];

    /**
     * 取得目前有效（啟用且在有效期間內）的分類折扣。
     * 若無則回傳 null。
     */
    public static function getActive(): ?self
    {
        return static::where('is_active', true)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->first();
    }

    /**
     * 取得設定記錄（只會有一筆），不存在則建立預設值。
     */
    public static function getSetting(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'is_active' => false,
                'discount_value' => 15,
                'categories' => [],
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDays(7)->toDateString(),
            ]
        );
    }
}
