<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $role = Role::where('name', 'admin')->first();
        if ($role) {
            $role->revokePermissionTo('fill assessment');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $role = Role::where('name', 'admin')->first();
        if ($role) {
            $role->givePermissionTo('fill assessment');
        }
    }
};
