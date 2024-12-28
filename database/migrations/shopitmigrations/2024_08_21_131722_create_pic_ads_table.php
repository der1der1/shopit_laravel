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
        Schema::create('pic_ads', function (Blueprint $table) {
            $table->comment('for testing the fuc\'n of pictures posting');
            $table->integer('id', true)->unique('id_unique');
            $table->string('name', 200);
            $table->string('img_dir', 200);

            $table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pic_ads');
    }
};
