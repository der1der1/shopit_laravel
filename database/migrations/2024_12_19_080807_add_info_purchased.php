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
        Schema::table('purchased', function (Blueprint $table) {

            $table->after('bill', function ($table) {
                $table->string('to_shop', 45)->nullable();
                $table->string('to_address', 45)->nullable();
                $table->string('bank_account', 25)->nullable();
                $table->integer('shop1_addr2')->nullable();
            });
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchased', function (Blueprint $table) {
            $table->dropColumn('to_shop');
            $table->dropColumn('to_address');
            $table->dropColumn('bank_account');
            $table->dropColumn('shop1_addr2');
        });
    }
};
