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
        Schema::table('ami_submissions', function (Blueprint $table) {
            $table->json('question_statuses')->nullable()->after('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::table('ami_submissions', function (Blueprint $table) {
            $table->dropColumn('question_statuses');
        });
    }
};
