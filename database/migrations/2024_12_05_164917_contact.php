<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('contact', function (Blueprint $table) {
            $table->increments('id')->primary();
            $table->string('name',255)->nullable()->index();
            $table->string('email', 255);
            $table->string('phone', 255);
            $table->string('information',5000); // 約 200中 300英
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact');
        
    }
};
