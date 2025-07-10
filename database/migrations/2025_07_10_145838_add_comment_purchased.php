<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchased', function (Blueprint $table) {
            $table->string('purchased')->comment('id, num, price')->change();
        });
    }

    public function down(): void
    {
        Schema::table('purchased', function (Blueprint $table) {
            $table->string('purchased')->comment('')->change();
        });
    }
};
