<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('permissions')->updateOrInsert(
            ['slug' => 'manage-member-status'],
            [
                'name' => 'Manage Member Status',
                'description' => 'Activate and deactivate members',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $roleId = DB::table('roles')->where('slug', 'member-manager')->value('id');
        $permissionId = DB::table('permissions')->where('slug', 'manage-member-status')->value('id');

        if ($roleId && $permissionId) {
            DB::table('role_permissions')->insertOrIgnore([
                'role_id' => $roleId,
                'permission_id' => $permissionId,
            ]);
        }
    }

    public function down(): void
    {
        // This permission predates the migration and may be assigned to custom roles.
    }
};
