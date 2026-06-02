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
     * 取得所有目前有效（啟用且在有效期間內）的分類折扣。
     */
    public static function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('is_active', true)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->get();
    }

    /**
     * 取得所有其他折扣單已佔用的分類（排除指定 id）。
     */
    public static function categoriesUsedByOthers(int $excludeId): array
    {
        return static::where('id', '!=', $excludeId)
            ->pluck('categories')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
