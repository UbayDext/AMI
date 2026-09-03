<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ami_auditee_assignment_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cycle_id')->constrained('ami_cycles')->cascadeOnDelete();
            $table->foreignId('standard_id')->constrained('standards')->restrictOnDelete();
            $table->foreignId('prodi_id')->constrained('prodis')->restrictOnDelete();
            $table->enum('assignment_mode', ['all_auditees', 'selected'])->default('all_auditees');
            $table->boolean('can_create')->default(true);
            $table->boolean('can_edit')->default(true);
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();
            $table->unique(['cycle_id', 'standard_id', 'prodi_id'], 'ami_auditee_group_scope_unique');
        });

        Schema::create('ami_auditee_assignment_members', function (Blueprint $table) {
            $table->foreignId('assignment_group_id')->constrained('ami_auditee_assignment_groups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->boolean('can_edit')->default(true);
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            $table->primary(['assignment_group_id', 'user_id'], 'ami_auditee_group_member_primary');
            $table->index(['user_id', 'can_edit'], 'ami_auditee_member_access_idx');
        });

        Schema::table('ami_submissions', function (Blueprint $table) {
            $table->foreignId('assignment_group_id')->nullable()->after('assignment_id')
                ->constrained('ami_auditee_assignment_groups')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ami_submissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assignment_group_id');
        });
        Schema::dropIfExists('ami_auditee_assignment_members');
        Schema::dropIfExists('ami_auditee_assignment_groups');
    }
};
