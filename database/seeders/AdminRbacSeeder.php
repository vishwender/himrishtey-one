<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Site;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminRbacSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        $superAdminRole = Role::updateOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Full access to all sites and admin functionality',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Super Admin gets ALL permissions
        |--------------------------------------------------------------------------
        */

        $superAdminRole->permissions()->sync(
            Permission::pluck('id')->toArray()
        );

        /*
        |--------------------------------------------------------------------------
        | Member Manager
        |--------------------------------------------------------------------------
        */

        $memberManagerRole = Role::updateOrCreate(
            ['slug' => 'member-manager'],
            [
                'name' => 'Member Manager',
                'description' => 'Manage members and member-related functionality',
            ]
        );

        $memberManagerPermissions = [
            'view-members',
            'create-members',
            'edit-members',

            'view-photos',
            'manage-member-photos',

            'manage-member-status',
            'manage-member-visibility',
            'manage-member-trusted',
            'manage-member-promoted',

            'advanced-search-members',

            'view-own-rotations',
            'create-rotations',
            'edit-rotations',
            'complete-rotations',
            'cancel-rotations',
        ];

        $memberManagerRole->permissions()->sync(
            Permission::whereIn(
                'slug',
                $memberManagerPermissions
            )->pluck('id')->toArray()
        );

        /*
        |--------------------------------------------------------------------------
        | Rotation Manager
        |--------------------------------------------------------------------------
        */

        $rotationManagerRole = Role::updateOrCreate(
            ['slug' => 'rotation-manager'],
            [
                'name' => 'Rotation Manager',
                'description' => 'Manage and monitor member rotations',
            ]
        );

        $rotationManagerPermissions = [
            'view-members',

            'view-own-rotations',
            'view-all-rotations',

            'create-rotations',
            'edit-rotations',
            'complete-rotations',
            'cancel-rotations',
        ];

        $rotationManagerRole->permissions()->sync(
            Permission::whereIn(
                'slug',
                $rotationManagerPermissions
            )->pluck('id')->toArray()
        );

        /*
        |--------------------------------------------------------------------------
        | Viewer
        |--------------------------------------------------------------------------
        */

        $viewerRole = Role::updateOrCreate(
            ['slug' => 'viewer'],
            [
                'name' => 'Viewer',
                'description' => 'View-only access to members and rotations',
            ]
        );

        $viewerPermissions = [
            'view-members',
            'view-photos',
            'view-own-rotations',
        ];

        $viewerRole->permissions()->sync(
            Permission::whereIn(
                'slug',
                $viewerPermissions
            )->pluck('id')->toArray()
        );

        /*
        |--------------------------------------------------------------------------
        | Super Admin Account
        |--------------------------------------------------------------------------
        */

        $admin = Admin::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'profile_id' => 1,
                'password' => Hash::make('ChangeMe@123'),
                'status' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Assign Super Admin Role
        |--------------------------------------------------------------------------
        */

        $admin->roles()->syncWithoutDetaching([
            $superAdminRole->id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Give Super Admin Access To All Active Sites
        |--------------------------------------------------------------------------
        */

        $admin->sites()->sync(
            Site::where('status', true)
                ->pluck('id')
                ->toArray()
        );

        /*
        |--------------------------------------------------------------------------
        | Output
        |--------------------------------------------------------------------------
        */

        $this->command->info(
            'Admin RBAC seeded successfully.'
        );

        $this->command->info(
            'Roles created: Super Admin, Member Manager, Rotation Manager, Viewer'
        );

        $this->command->info(
            'Super Admin: admin@example.com'
        );

        $this->command->info(
            'Password: ChangeMe@123'
        );
    }
}
