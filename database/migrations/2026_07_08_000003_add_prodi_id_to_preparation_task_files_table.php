<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('preparation_task_files', function (Blueprint $table) {
            $table->foreignId('prodi_id')->nullable()->after('task_id')
                ->constrained('prodis')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('preparation_task_files', function (Blueprint $table) {
            $table->dropForeign(['prodi_id']);
            $table->dropColumn('prodi_id');
        });
    }
};
