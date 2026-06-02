<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponSettingModel extends Model
{
    protected $table = 'coupon_settings';

    protected $fillable = [
        'allow_stacking',
        'updated_by',
    ];

    protected $casts = [
        'allow_stacking' => 'boolean',
    ];

    /**
     * 取得設定記錄（只會有一筆），不存在則建立預設值。
     */
    public static function getSetting(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'allow_stacking' => false,
                'updated_by' => null,
            ]
        );
    }

    /**
     * 取得最後修改的管理員。
     */
    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
