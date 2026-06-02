<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponCodeModel extends Model
{
    protected $table = 'coupon_codes';

    protected $fillable = [
        'title',
        'code',
        'discount_value',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
    ];

    /**
     * 取得目前有效且啟用中的折扣碼（供前台結帳驗證使用）。
     */
    public static function getActiveByCode(string $code): ?self
    {
        $today = now()->toDateString();

        return self::where('code', strtoupper($code))
            ->where('is_active', true)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();
    }
}
