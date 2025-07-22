<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_list', function (Blueprint $table) {
            $table->id(); 
            $table->string('name');
            $table->string('title');
            $table->string('email');
            $table->boolean('onoff')->default(1); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_list');
    }
};
