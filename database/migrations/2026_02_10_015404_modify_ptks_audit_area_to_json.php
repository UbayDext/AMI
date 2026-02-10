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
        Schema::table('ptks', function (Blueprint $table) {
            $table->dropForeign(['audit_area_id']);
            $table->dropColumn('audit_area_id');
            $table->json('audit_area_ids')->nullable()->after('standard_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ptks', function (Blueprint $table) {
            $table->dropColumn('audit_area_ids');
            $table->foreignId('audit_area_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};
