<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('audit_areas', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('code')->index();
            $table->unsignedInteger('sort_order')->default(0)->after('is_active');
            $table->unique('code');
        });

        Schema::create('standard_audit_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standard_id')->constrained()->cascadeOnDelete();
            $table->foreignId('audit_area_id')->constrained()->cascadeOnDelete();
            $table->string('auditee')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['standard_id', 'audit_area_id', 'auditee'], 'standard_area_auditee_unique');
        });

        Schema::table('ami_checklist_questions', function (Blueprint $table) {
            $table->foreignId('audit_area_id')->nullable()->after('standard_id')->constrained()->nullOnDelete();
        });

        Schema::table('ami_question_import_batches', function (Blueprint $table) {
            $table->foreignId('standard_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        Schema::table('ami_question_import_rows', function (Blueprint $table) {
            $table->foreignId('audit_area_id')->nullable()->after('standard_id')->constrained()->nullOnDelete();
        });

        DB::table('ami_checklist_questions')->whereNotNull('bidang')->orderBy('id')->eachById(function ($question) {
            $areaId = DB::table('audit_areas')->where('code', $question->bidang)->value('id');
            if ($areaId) DB::table('ami_checklist_questions')->where('id', $question->id)->update(['audit_area_id' => $areaId]);
        });
    }

    public function down(): void
    {
        Schema::table('ami_question_import_rows', fn (Blueprint $table) => $table->dropConstrainedForeignId('audit_area_id'));
        Schema::table('ami_question_import_batches', fn (Blueprint $table) => $table->dropConstrainedForeignId('standard_id'));
        Schema::table('ami_checklist_questions', fn (Blueprint $table) => $table->dropConstrainedForeignId('audit_area_id'));
        Schema::dropIfExists('standard_audit_areas');
        Schema::table('audit_areas', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn(['is_active', 'sort_order']);
        });
    }
};
