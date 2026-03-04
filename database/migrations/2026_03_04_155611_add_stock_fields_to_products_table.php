<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('selected')->comment('1=上架 0=下架');
            $table->integer('quantity')->default(0)->after('is_active')->comment('商品庫存數');
            $table->integer('min_quantity')->default(0)->after('quantity')->comment('最低庫存數');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'quantity', 'min_quantity']);
        });
    }
};
