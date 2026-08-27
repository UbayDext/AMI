<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fosk_criteria', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10);          // K1, K2, ... K9
            $table->string('name');               // Pendidikan, Penelitian, etc.
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fosk_criteria');
    }
};
