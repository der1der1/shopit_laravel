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
        Schema::create('word ads 2', function (Blueprint $table) {
            $table->comment('for advertisement words');
            $table->integer('id', true)->unique('id_unique');
            $table->string('words', 200)->unique('words_unique');

            $table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('word ads 2');
    }
};
