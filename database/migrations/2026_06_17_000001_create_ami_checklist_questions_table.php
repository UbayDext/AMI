<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ami_checklist_questions', function (Blueprint $table) {
            $table->id();
            $table->string('standard_code', 10); // ST01, ST02, ...
            $table->string('standard_name');
            $table->string('bidang', 10);         // WK1, WK2, WK3
            $table->text('auditi')->nullable();
            $table->unsignedSmallInteger('question_number');
            $table->text('question_text');
            $table->timestamps();

            $table->index('standard_code');
            $table->unique(['standard_code', 'question_number'], 'ami_checklist_standard_number_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ami_checklist_questions');
    }
};
