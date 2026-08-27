<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('preparation_tasks', function (Blueprint $table) {
            $table->dropForeign(['done_by']);
            $table->dropColumn(['is_done', 'done_at', 'done_by']);
        });
    }

    public function down(): void
    {
        Schema::table('preparation_tasks', function (Blueprint $table) {
            $table->boolean('is_done')->default(false)->after('sort_order');
            $table->timestamp('done_at')->nullable()->after('is_done');
            $table->foreignId('done_by')->nullable()->after('done_at')
                ->constrained('users')->nullOnDelete();
        });
    }
};
