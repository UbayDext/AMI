<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('preparation_stages', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('is_active')
                ->constrained('users')->nullOnDelete();
        });

        Schema::table('preparation_tasks', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('done_by')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('preparation_stages', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });

        Schema::table('preparation_tasks', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
};
