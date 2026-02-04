<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('findings', function (Blueprint $table) {
            // hapus unique lama: UNIQUE(code)
            $table->dropUnique('findings_code_unique');

            // unique baru: dalam 1 assessment saja
            $table->unique(['assessment_id', 'code'], 'findings_assessment_code_unique');

            // (opsional tapi bagus) pastikan sequence unik per assessment
            $table->unique(['assessment_id', 'sequence'], 'findings_assessment_sequence_unique');
        });
    }

    public function down(): void
    {
        Schema::table('findings', function (Blueprint $table) {
            $table->dropUnique('findings_assessment_code_unique');
            $table->dropUnique('findings_assessment_sequence_unique');

            $table->unique('code', 'findings_code_unique');
        });
    }
};
