<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('preparation_stages', function (Blueprint $table) {
            $table->foreignId('standard_id')
                ->nullable()
                ->after('accreditation_year_id')
                ->constrained('standards')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('preparation_stages', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Standard::class);
            $table->dropColumn('standard_id');
        });
    }
};
