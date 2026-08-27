<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop FKs that reference submission_questions
        Schema::table('submission_evidences', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
        });
        Schema::table('review_answers', function (Blueprint $table) {
            try { $table->dropForeign(['question_id']); } catch (\Throwable) {}
        });
        Schema::table('review_findings', function (Blueprint $table) {
            try { $table->dropForeign(['question_id']); } catch (\Throwable) {}
        });

        // 2. Drop submission_questions (no longer needed)
        Schema::dropIfExists('submission_questions');

        // 3. Rebuild submission_evidences columns
        Schema::table('submission_evidences', function (Blueprint $table) {
            // Rename question_id → ami_question_id
            $table->renameColumn('question_id', 'ami_question_id');
        });
        Schema::table('submission_evidences', function (Blueprint $table) {
            // Change FK target to ami_checklist_questions
            $table->foreign('ami_question_id')
                ->references('id')->on('ami_checklist_questions')
                ->nullOnDelete();

            // Add preparation_file_id for "pick from standar" evidence
            $table->foreignId('preparation_file_id')
                ->nullable()
                ->after('ami_question_id')
                ->constrained('preparation_task_files')
                ->nullOnDelete();

            // Composite index for fast per-question-per-category lookup
            $table->index(['submission_id', 'ami_question_id', 'category'],
                'se_submission_question_category_idx');
        });

        // 4. Fix review_answers.question_id → ami_checklist_questions
        Schema::table('review_answers', function (Blueprint $table) {
            $table->foreign('question_id')
                ->references('id')->on('ami_checklist_questions')
                ->nullOnDelete();
        });

        // 5. Fix review_findings.question_id → ami_checklist_questions
        Schema::table('review_findings', function (Blueprint $table) {
            $table->foreign('question_id')
                ->references('id')->on('ami_checklist_questions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('submission_evidences', function (Blueprint $table) {
            try { $table->dropForeign(['ami_question_id']); } catch (\Throwable) {}
            try { $table->dropForeign(['preparation_file_id']); } catch (\Throwable) {}
            try { $table->dropIndex('se_submission_question_category_idx'); } catch (\Throwable) {}
            $table->dropColumn('preparation_file_id');
            $table->renameColumn('ami_question_id', 'question_id');
        });
        Schema::table('review_answers', function (Blueprint $table) {
            try { $table->dropForeign(['question_id']); } catch (\Throwable) {}
        });
        Schema::table('review_findings', function (Blueprint $table) {
            try { $table->dropForeign(['question_id']); } catch (\Throwable) {}
        });
    }
};
