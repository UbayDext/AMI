<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('findings', function (Blueprint $table) {
            $table->foreignId('question_id')->nullable()->after('assessment_id')
                ->constrained('questions')->nullOnDelete();

            $table->text('condition_desc')->nullable()->after('description');   // Deskripsi Kondisi
            $table->text('root_cause')->nullable()->after('condition_desc');   // Akar Penyebab
            $table->text('impact')->nullable()->after('root_cause');           // Akibat
            $table->text('recommendation')->nullable()->after('impact');       // Rekomendasi
            $table->string('category', 50)->nullable()->after('recommendation'); // Kategori (Observasi/KTS/OFI)
            $table->text('corrective_plan')->nullable()->after('category');    // Rencana Perbaikan (Auditee)
            $table->date('due_date')->nullable()->after('corrective_plan');    // Due Date

            $table->unique(['assessment_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::table('findings', function (Blueprint $table) {
            $table->dropUnique(['assessment_id', 'question_id']);
            $table->dropConstrainedForeignId('question_id');
            $table->dropColumn([
                'condition_desc','root_cause','impact','recommendation',
                'category','corrective_plan','due_date',
            ]);
        });
    }
};
