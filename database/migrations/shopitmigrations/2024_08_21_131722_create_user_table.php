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
        Schema::create('user', function (Blueprint $table) {
            $table->comment('all users\' data in situ');
            $table->integer('user_id', true);
            $table->string('account', 45);
            $table->string('password', 45);
            $table->string('prvilige', 45);
            $table->string('name', 45)->nullable();
            $table->string('to_shop', 45)->nullable();
            $table->string('to_address', 45)->nullable();
            $table->string('bank_account', 45)->nullable();
            $table->integer('shop1_addr2')->nullable();
            $table->string('info0', 2000)->nullable();
            $table->string('info1', 2000)->nullable();
            $table->string('info2', 2000)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
