<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel submission_references (REFERENSI — link + title manual)
        Schema::create('submission_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')
                ->constrained('ami_submissions')->cascadeOnDelete();
            $table->foreignId('ami_question_id')
                ->constrained('ami_checklist_questions')->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('url', 2048);
            $table->text('notes')->nullable();
            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['submission_id', 'ami_question_id']);
        });

        // 2. Revisi submission_evidences (BUKTI — hanya dari prep files, hapus title/url)
        Schema::table('submission_evidences', function (Blueprint $table) {
            $table->dropColumn(['title', 'url']);
        });

        // 3. Tambah UNIQUE di submission_evidences (cegah file sama di kategori+pertanyaan sama)
        Schema::table('submission_evidences', function (Blueprint $table) {
            $table->unique(
                ['submission_id', 'ami_question_id', 'category', 'preparation_file_id'],
                'se_unique_file_per_question_category'
            );
        });

        // 4. Tambah UNIQUE di review_answers (cegah race condition updateOrCreate)
        Schema::table('review_answers', function (Blueprint $table) {
            $table->unique(['review_id', 'question_id'], 'ra_unique_review_question');
        });
    }

    public function down(): void
    {
        Schema::table('review_answers', function (Blueprint $table) {
            $table->dropUnique('ra_unique_review_question');
        });
        Schema::table('submission_evidences', function (Blueprint $table) {
            $table->dropUnique('se_unique_file_per_question_category');
            $table->string('title', 255)->nullable()->after('category');
            $table->string('url', 2048)->nullable()->after('title');
        });
        Schema::dropIfExists('submission_references');
    }
};
