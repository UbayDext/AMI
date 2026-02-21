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
            $table->text('realisasi')->nullable()->after('due_date');
            $table->string('efektifitas')->nullable()->after('realisasi');
            $table->string('tl_status')->nullable()->after('efektifitas');
        });
    }

    public function down(): void
    {
        Schema::table('ptks', function (Blueprint $table) {
            $table->dropColumn(['realisasi', 'efektifitas', 'tl_status']);
        });
    }
};
