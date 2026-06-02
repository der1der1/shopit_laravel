<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 建立通用折扣碼資料表
     */
    public function up(): void
    {
        Schema::create('coupon_codes', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100)->comment('折扣碼標題');
            $table->string('code', 50)->unique()->comment('折扣代碼（大寫英數字）');
            $table->tinyInteger('discount_value')->unsigned()->default(10)->comment('折扣比例 1-99（%），例如 15 = 八五折');
            $table->date('start_date')->comment('有效期間開始');
            $table->date('end_date')->comment('有效期間結束');
            $table->boolean('is_active')->default(false)->comment('是否啟用');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_codes');
    }
};
