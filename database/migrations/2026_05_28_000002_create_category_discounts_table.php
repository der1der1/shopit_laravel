<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_discounts', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(false);
            $table->unsignedTinyInteger('discount_value')->default(15);
            $table->json('categories')->nullable()->comment('套用折扣的商品分類清單');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_discounts');
    }
};
