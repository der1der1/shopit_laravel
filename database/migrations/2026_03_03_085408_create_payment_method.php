<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('method_name', 20)->unique()->comment('支付方式名稱');
            $table->text('description')->nullable()->comment('支付方式描述');
            $table->string('api_endpoint', 255)->nullable()->comment('API 端點');
            $table->string('icon', 255)->nullable()->comment('圖標路徑');
            
            // 生產環境金鑰
            $table->text('merchant_id')->nullable()->comment('商戶 ID');
            $table->text('api_key')->nullable()->comment('API 金鑰');
            $table->text('api_secret')->nullable()->comment('API 密鑰');
            
            // 測試環境金鑰
            $table->text('sandbox_merchant_id')->nullable()->comment('測試商戶 ID');
            $table->text('sandbox_api_key')->nullable()->comment('測試 API 金鑰');
            $table->text('sandbox_api_secret')->nullable()->comment('測試 API 密鑰');
            
            // 其他配置
            $table->integer('display_order')->default(0)->comment('顯示順序');
            
            // 手續費設定
            $table->decimal('fee_percentage', 5, 2)->default(0)->comment('手續費百分比');
            $table->decimal('fee_fixed', 10, 2)->default(0)->comment('固定手續費');
            
            $table->string('status', 20)->default('active')->comment('狀態 active/inactive/delete');
            $table->timestamps();

            // 索引
            $table->index('status');
            $table->index('display_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
