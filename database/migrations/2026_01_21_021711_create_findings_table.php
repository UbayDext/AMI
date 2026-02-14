<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->nullable()->constrained('questions')->nullOnDelete();
            $table->foreignId('standard_id')->constrained()->cascadeOnDelete();
            $table->json('audit_area_ids')->nullable();

            $table->unsignedInteger('sequence');
            $table->string('code');

            $table->string('title');
            $table->text('description')->nullable();
            $table->text('condition_desc')->nullable();
            $table->text('root_cause')->nullable();
            $table->text('impact')->nullable();
            $table->text('recommendation')->nullable();
            $table->string('category', 50)->nullable();
            $table->text('corrective_plan')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('severity', ['minor', 'major', 'critical'])->default('minor');
            $table->timestamps();

            $table->unique(['assessment_id', 'question_id']);
            $table->unique(['assessment_id', 'code'], 'findings_assessment_code_unique');
            $table->unique(['assessment_id', 'sequence'], 'findings_assessment_sequence_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('findings');
    }
};
