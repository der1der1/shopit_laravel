<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mail_list', function (Blueprint $table) {
            $table->boolean('stock_notification')->default(false)->after('onoff')->comment('接收數量不足通知');
        });
    }

    public function down(): void
    {
        Schema::table('mail_list', function (Blueprint $table) {
            $table->dropColumn('stock_notification');
        });
    }
};
