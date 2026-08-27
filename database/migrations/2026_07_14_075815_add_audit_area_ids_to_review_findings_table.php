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
        Schema::table('review_findings', function (Blueprint $table) {
            $table->json('audit_area_ids')->nullable()->after('question_id');
        });
    }

    public function down(): void
    {
        Schema::table('review_findings', function (Blueprint $table) {
            $table->dropColumn('audit_area_ids');
        });
    }
};
