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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account', 255)->unique()->nullable();
            $table->string('password',  255)->nullable();
            $table->string('prvilige', 45)->nullable();
            $table->string('name', 45)->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->string('to_shop', 45)->nullable();
            $table->string('to_address', 45)->nullable();
            $table->string('bank_account', 25)->nullable();
            $table->integer('shop1_addr2')->nullable();
            $table->string('info0', 2000)->nullable();
            $table->string('info1', 2000)->nullable();
            $table->string('info2', 2000)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
