<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('preparation_task_prodi', function (Blueprint $table) {
            $table->foreignId('preparation_task_id')->constrained('preparation_tasks')->cascadeOnDelete();
            $table->foreignId('prodi_id')->constrained('prodis')->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['preparation_task_id', 'prodi_id']);
            $table->index(['prodi_id', 'preparation_task_id']);
        });

        DB::table('preparation_tasks')
            ->whereNotNull('prodi_id')
            ->orderBy('id')
            ->each(function ($task): void {
                DB::table('preparation_task_prodi')->insertOrIgnore([
                    'preparation_task_id' => $task->id,
                    'prodi_id' => $task->prodi_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('preparation_task_prodi');
    }
};
