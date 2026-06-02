<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 全站折扣設定表（單列設定表，最多一筆資料）
     */
    public function up(): void
    {
        Schema::create('sitewide_discounts', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(false)->comment('是否啟用全站折扣');
            $table->unsignedTinyInteger('discount_value')->default(10)->comment('折扣百分比（1-99），例如 10 代表打九折');
            $table->date('start_date')->comment('折扣開始日期');
            $table->date('end_date')->comment('折扣結束日期');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sitewide_discounts');
    }
};
