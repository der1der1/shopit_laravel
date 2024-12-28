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
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pic_name', 45)->nullable();
            $table->string('pic_dir', 225);
            $table->string('product_name', 45);
            $table->string('description', 225);
            $table->string('price', 45);
            $table->string('ori_price', 45);
            $table->string('category', 45);
            $table->string('selected', 45)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
