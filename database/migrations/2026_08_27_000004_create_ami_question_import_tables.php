<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ami_question_import_batches', function (Blueprint $table) {
            $table->id();
            $table->string('original_filename');
            $table->string('file_hash', 64)->index();
            $table->string('status', 20)->default('validating')->index();
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('valid_rows')->default(0);
            $table->unsignedInteger('invalid_rows')->default(0);
            $table->unsignedInteger('duplicate_rows')->default(0);
            $table->unsignedInteger('imported_rows')->default(0);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->text('error_message')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::table('ami_checklist_questions', function (Blueprint $table) {
            $table->foreignId('standard_id')->nullable()->after('id')->constrained('standards')->nullOnDelete();
            $table->foreignId('import_batch_id')->nullable()->after('question_text')->constrained('ami_question_import_batches')->nullOnDelete();
            $table->text('reference')->nullable()->after('question_text');
            $table->boolean('is_required')->default(true)->after('reference');
            $table->boolean('is_active')->default(true)->after('is_required')->index();
            $table->unsignedInteger('sort_order')->default(0)->after('is_active');
            $table->string('source_hash', 64)->nullable()->after('sort_order')->index();
            $table->foreignId('created_by')->nullable()->after('source_hash')->constrained('users')->nullOnDelete();
            $table->index(['standard_code', 'is_active', 'sort_order'], 'ami_questions_standard_active_order');
        });

        Schema::create('ami_checklist_question_prodi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('ami_checklist_questions')->cascadeOnDelete();
            $table->foreignId('prodi_id')->constrained('prodis')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['question_id', 'prodi_id']);
            $table->index(['prodi_id', 'question_id']);
        });

        Schema::create('ami_question_import_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')->constrained('ami_question_import_batches')->cascadeOnDelete();
            $table->unsignedInteger('row_number');
            $table->string('standard_code', 20)->nullable();
            $table->foreignId('standard_id')->nullable()->constrained('standards')->nullOnDelete();
            $table->unsignedSmallInteger('question_number')->nullable();
            $table->text('question_text')->nullable();
            $table->text('reference')->nullable();
            $table->string('bidang', 50)->nullable();
            $table->text('auditi')->nullable();
            $table->json('prodi_codes')->nullable();
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('source_hash', 64)->nullable()->index();
            $table->string('status', 20)->default('valid')->index();
            $table->json('validation_errors')->nullable();
            $table->foreignId('generated_question_id')->nullable()->constrained('ami_checklist_questions')->nullOnDelete();
            $table->timestamps();
            $table->index(['import_batch_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ami_question_import_rows');
        Schema::dropIfExists('ami_checklist_question_prodi');
        Schema::table('ami_checklist_questions', function (Blueprint $table) {
            $table->dropIndex('ami_questions_standard_active_order');
            $table->dropConstrainedForeignId('standard_id');
            $table->dropConstrainedForeignId('import_batch_id');
            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn(['reference', 'is_required', 'is_active', 'sort_order', 'source_hash']);
        });
        Schema::dropIfExists('ami_question_import_batches');
    }
};
