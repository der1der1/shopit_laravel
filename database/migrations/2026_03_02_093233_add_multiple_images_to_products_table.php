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
        Schema::table('products', function (Blueprint $table) {
            // JSON 欄位儲存額外圖片的名稱和路徑（最多 3 張）
            // pic_name 和 pic_dir 維持作為預設第一張圖片
            $table->json('pic_name_more')->nullable()->after('pic_name')->comment('額外圖片名稱（第2-4張）');
            $table->json('pic_dir_more')->nullable()->after('pic_dir')->comment('額外圖片路徑（第2-4張）');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['pic_name_more', 'pic_dir_more']);
        });
    }
};
