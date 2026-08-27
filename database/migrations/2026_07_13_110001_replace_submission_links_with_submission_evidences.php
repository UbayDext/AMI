<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('submission_links');

        Schema::create('submission_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('ami_submissions')->cascadeOnDelete();
            $table->foreignId('ami_question_id')->nullable()->constrained('ami_checklist_questions')->nullOnDelete();
            $table->enum('category', ['kebijakan', 'pelaksanaan', 'evaluasi', 'pendukung_digital'])->default('kebijakan');
            $table->string('title');
            $table->string('url', 2048);
            $table->text('notes')->nullable();
            $table->foreignId('added_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['submission_id', 'ami_question_id']);
            $table->index(['submission_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_evidences');

        Schema::create('submission_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('ami_submissions')->cascadeOnDelete();
            $table->string('title');
            $table->string('url', 2048);
            $table->foreignId('added_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->index('submission_id');
        });
    }
};
