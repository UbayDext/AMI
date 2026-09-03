<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('onboarding_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('onboarding_key', 100);
            $table->unsignedInteger('version')->default(1);
            $table->unsignedSmallInteger('current_step')->default(0);
            $table->string('status', 20)->default('started');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'onboarding_key', 'version'], 'user_onboarding_version_unique');
            $table->index(['onboarding_key', 'status']);
        });
    }

    public function down(): void { Schema::dropIfExists('onboarding_progress'); }
};
