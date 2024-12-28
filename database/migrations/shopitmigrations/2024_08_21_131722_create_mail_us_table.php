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
        Schema::create('mail_us', function (Blueprint $table) {
            $table->comment('Recording some suggessions for us');
            $table->integer('id', true)->unique('id_unique');
            $table->string('name', 45);
            $table->string('phone', 45)->nullable();
            $table->string('email', 45);
            $table->string('message', 600);

            $table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_us');
    }
};
