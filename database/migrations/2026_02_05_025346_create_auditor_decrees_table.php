<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('auditor_decrees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accreditation_year_id')->nullable()->constrained()->nullOnDelete();

            $table->string('period_label')->nullable(); // contoh: "2025" / "Periode AMI 2025"
            $table->string('decree_number')->nullable();
            $table->date('decree_date')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();

            $table->string('file_path')->nullable();
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditor_decrees');
    }
};
