<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('specialization');
            $table->string('phone');
            $table->string('email')->unique();
            $table->timestamps();
            $table->foreign('id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
