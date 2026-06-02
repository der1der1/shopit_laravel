<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 優惠券全域設定表（單列設定表，最多一筆資料）
     */
    public function up(): void
    {
        Schema::create('coupon_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('allow_stacking')->default(false)->comment('是否允許優惠疊加使用');
            $table->unsignedInteger('updated_by')->nullable()->comment('最後修改的管理員 user_id');
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_settings');
    }
};
