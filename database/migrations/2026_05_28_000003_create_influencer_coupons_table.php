<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('influencer_coupons', function (Blueprint $table) {
            $table->id();
            $table->string('name');                                         // 姓名 / 帳號名稱
            $table->string('social_link')->nullable();                      // 社群連結（IG / YouTube / TikTok 等）
            $table->string('email')->nullable();                            // 聯絡 Email
            $table->string('code')->unique();                               // 折扣代碼（唯一）
            $table->unsignedTinyInteger('discount_value')->default(10);    // 折扣百分比 1–99（例如 10 = 九折）
            $table->date('start_date');                                     // 有效開始日期
            $table->date('end_date');                                       // 有效結束日期
            $table->boolean('is_active')->default(false);                  // 是否啟用
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('influencer_coupons');
    }
};
