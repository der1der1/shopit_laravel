<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchased', function (Blueprint $table) {
            $table->after('account', function ($table) {
                $table->string('name', 45)->nullable();
            });
        }); 
    }

    public function down(): void
    {
        Schema::table('purchased', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
