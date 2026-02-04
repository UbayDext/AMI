<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            if (!Schema::hasColumn('answers', 'status')) {
                $table->string('status')->default('sesuai')->after('value_text');
            }
            if (!Schema::hasColumn('answers', 'reason')) {
                $table->text('reason')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            if (Schema::hasColumn('answers', 'reason')) $table->dropColumn('reason');
            if (Schema::hasColumn('answers', 'status')) $table->dropColumn('status');
        });
    }
};

