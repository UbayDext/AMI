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
        Schema::table('question_categories', function (Blueprint $table) {
            $table->dropColumn(['code', 'sort_order', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_categories', function (Blueprint $table) {
            $table->string('code', 50)->nullable()->after('parent_id');
            $table->integer('sort_order')->default(0)->after('name');
            $table->boolean('is_active')->default(true)->after('sort_order');
        });
    }
};
