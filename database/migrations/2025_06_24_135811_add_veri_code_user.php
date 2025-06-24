<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->after('name', function ($table) {
                $table->string('status', 45)->nullable();
                $table->string('veri_code', 10)->nullable();
            });
        }); 
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('veri_code');
        });
    }
};
