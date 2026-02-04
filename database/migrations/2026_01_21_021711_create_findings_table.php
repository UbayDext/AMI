<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('standard_id')->constrained()->cascadeOnDelete();
            $table->foreignId('audit_area_id')->constrained('audit_areas')->cascadeOnDelete();

            $table->unsignedInteger('sequence'); // 2,3,4...
            $table->string('code')->unique(); // PTK/002/ST1/WK2

            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('severity', ['minor', 'major', 'critical'])->default('minor');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('findings');
    }
};
