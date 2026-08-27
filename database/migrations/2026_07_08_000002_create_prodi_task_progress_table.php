<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prodi_task_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('preparation_tasks')->cascadeOnDelete();
            $table->foreignId('prodi_id')->constrained('prodis')->restrictOnDelete();

            $table->boolean('is_applicable')->default(true);
            $table->boolean('is_done')->default(false);
            $table->timestamp('done_at')->nullable();
            $table->foreignId('done_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['task_id', 'prodi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prodi_task_progress');
    }
};
