<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ami_auditee_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cycle_id')->constrained('ami_cycles')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('standard_id')->constrained('standards')->restrictOnDelete();
            $table->enum('prodi_scope', ['all', 'selected'])->default('all');
            $table->boolean('can_create')->default(true);
            $table->boolean('can_edit')->default(true);
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->unique(['cycle_id', 'user_id', 'standard_id'], 'ami_assignment_unique');
            $table->index(['user_id', 'cycle_id', 'standard_id'], 'ami_assignment_access_idx');
        });

        Schema::create('ami_assignment_prodis', function (Blueprint $table) {
            $table->foreignId('assignment_id')->constrained('ami_auditee_assignments')->cascadeOnDelete();
            $table->foreignId('prodi_id')->constrained('prodis')->restrictOnDelete();
            $table->timestamps();
            $table->primary(['assignment_id', 'prodi_id']);
        });

        Schema::table('ami_submissions', function (Blueprint $table) {
            $table->foreignId('owner_id')->nullable()->after('standard_id')->constrained('users')->nullOnDelete();
            $table->foreignId('assignment_id')->nullable()->after('owner_id')->constrained('ami_auditee_assignments')->nullOnDelete();
            $table->index(['owner_id', 'status'], 'ami_submission_owner_status_idx');
        });

        DB::table('ami_submissions')->whereNull('owner_id')->whereNotNull('submitted_by')
            ->update(['owner_id' => DB::raw('submitted_by')]);

        Schema::create('ami_submission_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('ami_submissions')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('ami_checklist_questions')->restrictOnDelete();
            $table->string('status', 100);
            $table->text('notes')->nullable();
            $table->foreignId('answered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['submission_id', 'question_id'], 'ami_submission_answer_unique');
            $table->index(['question_id', 'status']);
        });

        // Preserve existing JSON answers while all new writes use the relational table.
        if (Schema::hasColumn('ami_submissions', 'question_statuses')) {
            DB::table('ami_submissions')->whereNotNull('question_statuses')->orderBy('id')
                ->each(function ($submission) {
                    $answers = json_decode($submission->question_statuses, true);
                    foreach (is_array($answers) ? $answers : [] as $questionId => $answer) {
                        if (! is_array($answer) || ! isset($answer['status'])) {
                            continue;
                        }
                        DB::table('ami_submission_answers')->updateOrInsert(
                            ['submission_id' => $submission->id, 'question_id' => (int) $questionId],
                            [
                                'status' => $answer['status'],
                                'notes' => $answer['notes'] ?? null,
                                'answered_by' => $submission->submitted_by,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ami_submission_answers');
        Schema::table('ami_submissions', function (Blueprint $table) {
            $table->dropForeign(['assignment_id']);
            $table->dropForeign(['owner_id']);
            $table->dropIndex('ami_submission_owner_status_idx');
            $table->dropColumn(['assignment_id', 'owner_id']);
        });
        Schema::dropIfExists('ami_assignment_prodis');
        Schema::dropIfExists('ami_auditee_assignments');
    }
};
