<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Has access to everything in the system',
                'permissions' => [
                    'all',
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Branch Manager',
                'slug' => 'branch-manager',
                'description' => 'Manages a specific branch',
                'permissions' => [
                    'view-dashboard',
                    'manage-members',
                    'manage-loans',
                    'manage-savings',
                    'manage-transactions',
                    'view-reports',
                    'approve-loans',
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Field Officer',
                'slug' => 'field-officer',
                'description' => 'Handles field operations',
                'permissions' => [
                    'view-dashboard',
                    'view-members',
                    'create-members',
                    'view-loans',
                    'create-loans',
                    'view-savings',
                    'create-savings',
                    'view-transactions',
                    'create-transactions',
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Accountant',
                'slug' => 'accountant',
                'description' => 'Manages financial records',
                'permissions' => [
                    'view-dashboard',
                    'view-members',
                    'view-loans',
                    'view-savings',
                    'manage-transactions',
                    'manage-expenses',
                    'view-reports',
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Member',
                'slug' => 'member',
                'description' => 'Regular member of the microfinance',
                'permissions' => [
                    'view-own-profile',
                    'view-own-loans',
                    'view-own-savings',
                    'view-own-transactions',
                ],
                'status' => 'active',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
