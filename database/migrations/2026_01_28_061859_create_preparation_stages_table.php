<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('preparation_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accreditation_year_id')->nullable()
                ->constrained('accreditation_years')->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preparation_stages');
    }
};
