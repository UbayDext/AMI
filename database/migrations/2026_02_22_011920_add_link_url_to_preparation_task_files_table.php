<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('preparation_task_files', function (Blueprint $table) {
            $table->string('link_url')->nullable()->after('size');
        });
    }

    public function down(): void
    {
        Schema::table('preparation_task_files', function (Blueprint $table) {
            $table->dropColumn('link_url');
        });
    }
};
