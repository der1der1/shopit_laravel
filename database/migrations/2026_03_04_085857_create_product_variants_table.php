<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->string('variant_name', 100)->comment('品項名稱：黑色、紅色、三分螺絲等');
            $table->string('unicode', 50)->nullable()->unique()->comment('唯一識別碼');
            $table->decimal('price', 10, 2)->comment('售價');
            $table->decimal('ori_price', 10, 2)->nullable()->comment('原價');
            $table->boolean('use_oriprice')->default(false)->comment('是否使用原價');
            $table->integer('quantity')->default(0)->comment('庫存數量');
            $table->integer('min_quantity')->default(0)->comment('最低庫存數量');
            $table->string('pic_dir', 255)->nullable()->comment('品項專屬圖片路徑');
            $table->boolean('is_default')->default(false)->comment('是否為預設品項');
            $table->boolean('is_active')->default(true)->comment('上架');
            $table->integer('sort_order')->default(0)->comment('排序權重');
            $table->timestamps();
            
            // 外鍵約束
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
            
            // 索引
            $table->index(['product_id', 'is_active']);
            $table->index('unicode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
