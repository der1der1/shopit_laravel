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
        Schema::create('purchased', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account', 50);
            $table->string('purchased', 600)->nullable();
            $table->string('bill', 25);
            $table->string('payed', 5);
            $table->string('delivered', 5);
            $table->string('recieved', 5);
            $table->string('show', 5);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchased');
    }
};
