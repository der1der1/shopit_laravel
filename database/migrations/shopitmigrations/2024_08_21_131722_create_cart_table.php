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
        Schema::create('cart', function (Blueprint $table) {
            $table->comment('for entire users to store their own fuck\'n stuffs');
            $table->integer('id', true);
            $table->string('user', 45);
            $table->string('product_id', 45);
            $table->string('product_name', 45);
            $table->string('product_pic_dir', 45);
            $table->string('price', 45);
            $table->string('num', 45);
            $table->string('buy_bool', 45);
            $table->string('product_description', 225);
            $table->string('buy_confirm', 45);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart');
    }
};
