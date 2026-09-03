<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $auditeeIds = DB::table('users')
                ->join('model_has_roles', function ($join) {
                    $join->on('model_has_roles.model_id', '=', 'users.id')
                        ->where('model_has_roles.model_type', '=', 'App\\Models\\User');
                })
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->where('roles.name', 'auditee')
                ->where('users.is_active', true)
                ->distinct()->pluck('users.id');

            DB::table('ami_submissions')->whereNull('assignment_group_id')->orderBy('id')->each(function ($submission) use ($auditeeIds) {
                $now = now();
                $groupId = DB::table('ami_auditee_assignment_groups')->insertGetId([
                    'cycle_id' => $submission->cycle_id,
                    'standard_id' => $submission->standard_id,
                    'prodi_id' => $submission->prodi_id,
                    'assignment_mode' => 'all_auditees',
                    'can_create' => true,
                    'can_edit' => true,
                    'assigned_by' => null,
                    'assigned_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('ami_submissions')->where('id', $submission->id)
                    ->update(['assignment_group_id' => $groupId]);

                $members = $auditeeIds->map(fn ($userId) => [
                    'assignment_group_id' => $groupId,
                    'user_id' => $userId,
                    'can_edit' => true,
                    'assigned_by' => null,
                    'joined_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();
                DB::table('ami_auditee_assignment_members')->insertOrIgnore($members);
            });
        });
    }

    public function down(): void
    {
        // Backfill is intentionally retained to avoid revoking existing access.
    }
};
