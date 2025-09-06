<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full system access with all permissions',
                'permissions' => [
                    'users.view',
                    'users.create',
                    'users.edit',
                    'users.delete',
                    'roles.view',
                    'roles.create',
                    'roles.edit',
                    'roles.delete',
                    'groups.view',
                    'groups.create',
                    'groups.edit',
                    'groups.delete',
                    'credentials.view',
                    'credentials.create',
                    'credentials.edit',
                    'credentials.delete',
                    'credentials.export',
                    'audit.view',
                    'system.manage',
                ],
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Group-level management with limited system access',
                'permissions' => [
                    'groups.view',
                    'groups.create',
                    'groups.edit',
                    'credentials.view',
                    'credentials.create',
                    'credentials.edit',
                    'credentials.delete',
                    'credentials.export',
                    'audit.view.own',
                ],
            ],
            [
                'name' => 'user',
                'display_name' => 'User',
                'description' => 'Basic user with read-only access',
                'permissions' => [
                    'groups.view',
                    'credentials.view',
                    'audit.view.own',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            \App\Models\Role::create($roleData);
        }
    }
}
