<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submission_evidences', function (Blueprint $table) {
            // Drop old UNIQUE that includes preparation_file_id
            $table->dropIndex('se_unique_file_per_question_category');

            // Drop FK to preparation_task_files
            $table->dropForeign(['preparation_file_id']);

            // Rename column
            $table->renameColumn('preparation_file_id', 'preparation_task_id');
        });

        Schema::table('submission_evidences', function (Blueprint $table) {
            // Add FK to preparation_tasks
            $table->foreign('preparation_task_id')
                ->references('id')
                ->on('preparation_tasks')
                ->nullOnDelete();

            // Restore UNIQUE constraint with new column name
            $table->unique(
                ['submission_id', 'ami_question_id', 'category', 'preparation_task_id'],
                'se_unique_task_per_question_category'
            );
        });
    }

    public function down(): void
    {
        Schema::table('submission_evidences', function (Blueprint $table) {
            $table->dropIndex('se_unique_task_per_question_category');
            $table->dropForeign(['preparation_task_id']);
            $table->renameColumn('preparation_task_id', 'preparation_file_id');
        });

        Schema::table('submission_evidences', function (Blueprint $table) {
            $table->foreign('preparation_file_id')
                ->references('id')
                ->on('preparation_task_files')
                ->nullOnDelete();

            $table->unique(
                ['submission_id', 'ami_question_id', 'category', 'preparation_file_id'],
                'se_unique_file_per_question_category'
            );
        });
    }
};
