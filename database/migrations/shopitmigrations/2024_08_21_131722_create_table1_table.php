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
        Schema::create('table1', function (Blueprint $table) {
            $table->integer('序號')->primary();
            $table->string('名稱', 45);
            $table->integer('評分')->nullable();

            $table->unique(['序號'], '序號_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table1');
    }
};
