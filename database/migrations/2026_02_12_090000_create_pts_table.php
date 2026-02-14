<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ptk_id')->constrained('ptks')->cascadeOnDelete();
            $table->text('realisasi')->nullable();
            $table->string('efektifitas')->nullable(); // Efektif, Belum Efektif
            $table->string('status')->default('Open'); // Open, Close
            $table->timestamps();

            $table->unique('ptk_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pts');
    }
};
