<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ptks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('standard_id')->nullable()->constrained()->nullOnDelete();

            $table->json('audit_area_ids')->nullable();
            $table->string('code')->nullable();
            $table->unsignedInteger('sequence')->nullable();

            // Temuan
            $table->string('category')->nullable();
            $table->text('condition_desc')->nullable();
            $table->text('root_cause')->nullable();
            $table->text('impact')->nullable();
            $table->text('recommendation')->nullable();
            $table->text('corrective_plan')->nullable();

            // Jadwal
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('due_date')->nullable();

            // Tindak lanjut
            $table->text('realisasi')->nullable();
            $table->string('efektifitas')->nullable();
            $table->string('tl_status')->nullable();
            $table->string('pic')->nullable();

            $table->timestamps();

            $table->unique(['assessment_id', 'question_id'], 'ptks_assessment_question_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ptks');
    }
};
