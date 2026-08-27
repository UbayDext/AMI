<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('ami_submissions')->cascadeOnDelete();
            $table->string('bidang', 20)->nullable();
            $table->text('question_text');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['submission_id', 'sort_order']);
        });

        // Ganti ami_question_id → question_id → submission_questions
        Schema::table('submission_evidences', function (Blueprint $table) {
            $table->dropForeign(['ami_question_id']);
        });
        Schema::table('submission_evidences', function (Blueprint $table) {
            $table->dropIndex(['submission_id', 'ami_question_id']);
        });
        Schema::table('submission_evidences', function (Blueprint $table) {
            $table->dropColumn('ami_question_id');
            $table->foreignId('question_id')->nullable()->after('submission_id')
                ->constrained('submission_questions')->nullOnDelete();
        });

        // Update FK di review_answers: question_id sekarang ke submission_questions
        Schema::table('review_answers', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
        });
        Schema::table('review_answers', function (Blueprint $table) {
            // Keep an index available for the review_id foreign key while replacing the composite index.
            $table->index('review_id');
        });
        Schema::table('review_answers', function (Blueprint $table) {
            $table->dropIndex(['review_id', 'question_id']);
        });
        Schema::table('review_answers', function (Blueprint $table) {
            $table->dropColumn('question_id');
            $table->foreignId('question_id')->nullable()->after('review_id')
                ->constrained('submission_questions')->nullOnDelete();
        });

        // Update FK di review_findings: question_id sekarang ke submission_questions
        Schema::table('review_findings', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
        });
        Schema::table('review_findings', function (Blueprint $table) {
            $table->dropColumn('question_id');
            $table->foreignId('question_id')->nullable()->after('review_id')
                ->constrained('submission_questions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('review_findings', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
            $table->dropColumn('question_id');
            $table->unsignedBigInteger('question_id')->nullable()->after('review_id');
        });

        Schema::table('review_answers', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
            $table->dropColumn('question_id');
            $table->unsignedBigInteger('question_id')->nullable()->after('review_id');
        });

        Schema::table('submission_evidences', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
            $table->dropColumn('question_id');
            $table->foreignId('ami_question_id')->nullable()->after('submission_id')
                ->constrained('ami_checklist_questions')->nullOnDelete();
            $table->index(['submission_id', 'ami_question_id']);
        });

        Schema::dropIfExists('submission_questions');
    }
};
