<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

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
                'permissions' => [
                    'manage_users',
                    'manage_roles',
                    'manage_branches',
                    'manage_members',
                    'manage_loans',
                    'manage_savings',
                    'manage_transactions',
                    'manage_expenses',
                    'view_reports',
                ],
            ],
            [
                'name' => 'Branch Manager',
                'slug' => 'branch-manager',
                'permissions' => [
                    'manage_members',
                    'manage_loans',
                    'manage_savings',
                    'manage_transactions',
                    'manage_expenses',
                    'view_reports',
                ],
            ],
            [
                'name' => 'Field Officer',
                'slug' => 'field-officer',
                'permissions' => [
                    'manage_members',
                    'manage_loans',
                    'manage_savings',
                    'manage_transactions',
                ],
            ],
            [
                'name' => 'Accountant',
                'slug' => 'accountant',
                'permissions' => [
                    'manage_transactions',
                    'manage_expenses',
                    'view_reports',
                ],
            ],
            [
                'name' => 'Member',
                'slug' => 'member',
                'permissions' => [
                    'view_own_loans',
                    'view_own_savings',
                    'view_own_transactions',
                ],
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
