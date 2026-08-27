<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fosk_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('criteria_id')->constrained('fosk_criteria')->cascadeOnDelete();
            $table->foreignId('accreditation_year_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['lkpt', 'lkpd']);  // kuantitatif / kualitatif
            $table->string('title');
            $table->text('description')->nullable();   // narasi deskriptif (LKPD)
            $table->text('data_value')->nullable();     // data kuantitatif (LKPT)
            $table->string('pic')->nullable();          // penanggung jawab
            $table->string('status')->default('draft'); // draft, review, final
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fosk_documents');
    }
};
