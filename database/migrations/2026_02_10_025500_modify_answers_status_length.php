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
        Schema::table('answers', function (Blueprint $table) {
            // Increase length to 255 (or whatever is sufficient, but 255 is standard)
            // Using change() requires doctrine/dbal, but let's try just resizing.
            // If it's already 255, something else is wrong.
            // But if it was implicitly created as something shorter...
            $table->string('status', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            // Revert if needed, but usually redundant for length changes upwards
        });
    }
};
