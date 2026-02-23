<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            // Stores an array of standard IDs that have been submitted individually.
            // e.g. [1, 3, 5]  = standards 1, 3, 5 have been submitted for this assessment.
            $table->json('submitted_standards')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn('submitted_standards');
        });
    }
};
