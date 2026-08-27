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
            $table->text('realisasi')->nullable()->after('tl_status');
            $table->string('efektifitas', 50)->nullable()->after('realisasi');
        });
    }

    public function down(): void
    {
        Schema::table('review_findings', function (Blueprint $table) {
            $table->dropColumn(['realisasi', 'efektifitas']);
        });
    }
};
