<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'manage-users',
            'manage-roles',
            'manage-permissions',
            'view-banks',
            'create-banks',
            'edit-banks',
            'delete-banks',
            'view-cards',
            'create-cards',
            'edit-cards',
            'delete-cards',
            'view-transactions',
            'create-transactions',
            'edit-transactions',
            'delete-transactions',
            'view-categories',
            'create-categories',
            'edit-categories',
            'delete-categories',
            'view-reports',
            'export-data',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles
        $superadmin = Role::create(['name' => 'superadmin']);
        $member = Role::create(['name' => 'member']);

        // Assign all permissions to superadmin
        $superadmin->givePermissionTo(Permission::all());

        // Assign specific permissions to member
        $member->givePermissionTo([
            'view-banks',
            'create-banks',
            'edit-banks',
            'delete-banks',
            'view-cards',
            'create-cards',
            'edit-cards',
            'delete-cards',
            'view-transactions',
            'create-transactions',
            'edit-transactions',
            'delete-transactions',
            'view-categories',
            'view-reports',
            'export-data',
        ]);

        // Create superadmin user
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@janganBoros.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('superadmin');

        // Create member user
        $user = User::create([
            'name' => 'Member User',
            'email' => 'user@janganBoros.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $user->assignRole('member');
    }
}
