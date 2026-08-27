<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ami_cycles')) {
            Schema::create('ami_cycles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('accreditation_year_id')->nullable()->constrained()->nullOnDelete();
                $table->string('title');
                $table->date('period_start')->nullable();
                $table->date('period_end')->nullable();
                $table->enum('status', ['draft', 'active', 'closed'])->default('draft')->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('ami_submissions')) {
            Schema::create('ami_submissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cycle_id')->constrained('ami_cycles')->cascadeOnDelete();
                $table->foreignId('prodi_id')->constrained('prodis')->restrictOnDelete();
                $table->foreignId('standard_id')->constrained('standards')->restrictOnDelete();
                $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
                $table->enum('status', ['draft', 'submitted', 'under_review', 'revision', 'accepted'])->default('draft');
                $table->timestamp('submitted_at')->nullable();
                $table->timestamps();

                $table->unique(['cycle_id', 'prodi_id', 'standard_id'], 'ami_submission_scope_unique');
                $table->index(['cycle_id', 'standard_id', 'status']);
            });
        }

        if (! Schema::hasTable('ami_reviews')) {
            Schema::create('ami_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('submission_id')->unique()->constrained('ami_submissions')->cascadeOnDelete();
                $table->foreignId('reviewer_id')->constrained('users')->restrictOnDelete();
                $table->foreignId('decree_id')->nullable()->constrained('auditor_decrees')->nullOnDelete();
                $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                $table->index(['reviewer_id', 'status']);
            });
        }

        if (! Schema::hasTable('review_answers')) {
            Schema::create('review_answers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('review_id')->constrained('ami_reviews')->cascadeOnDelete();
                $table->foreignId('question_id')->nullable()->constrained('ami_checklist_questions')->nullOnDelete();
                $table->string('status', 100)->default('belum_diperiksa');
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->index(['review_id', 'question_id']);
            });
        }

        if (! Schema::hasTable('review_findings')) {
            Schema::create('review_findings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('review_id')->constrained('ami_reviews')->cascadeOnDelete();
                $table->foreignId('question_id')->nullable()->constrained('ami_checklist_questions')->nullOnDelete();
                $table->string('category', 100);
                $table->text('condition_desc');
                $table->text('root_cause')->nullable();
                $table->text('impact')->nullable();
                $table->text('recommendation')->nullable();
                $table->text('corrective_plan')->nullable();
                $table->date('due_date')->nullable();
                $table->string('tl_status', 30)->nullable();
                $table->string('pic')->nullable();
                $table->timestamps();
                $table->index(['review_id', 'category']);
                $table->index(['tl_status', 'due_date']);
            });
        }
    }

    public function down(): void
    {
        // Deliberately non-destructive: these tables may predate this repository migration.
    }
};
