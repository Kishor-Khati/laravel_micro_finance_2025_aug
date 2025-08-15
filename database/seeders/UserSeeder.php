<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Get role IDs
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $branchManagerRole = Role::where('slug', 'branch-manager')->first();
        $fieldOfficerRole = Role::where('slug', 'field-officer')->first();
        $accountantRole = Role::where('slug', 'accountant')->first();
        
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@microfinance.com',
                'password' => Hash::make('password'),
                'role_id' => $superAdminRole->id,
                'employee_id' => 'EMP001',
                'phone' => '9876543210',
                'status' => 'active',
            ],
            [
                'name' => 'Kathmandu Manager',
                'email' => 'manager.ktm@microfinance.com',
                'password' => Hash::make('password'),
                'role_id' => $branchManagerRole->id,
                'branch_id' => 1, // Kathmandu branch
                'employee_id' => 'EMP002',
                'phone' => '9876543211',
                'status' => 'active',
            ],
            [
                'name' => 'Pokhara Manager',
                'email' => 'manager.pkr@microfinance.com',
                'password' => Hash::make('password'),
                'role_id' => $branchManagerRole->id,
                'branch_id' => 2, // Pokhara branch
                'employee_id' => 'EMP003',
                'phone' => '9876543212',
                'status' => 'active',
            ],
            [
                'name' => 'Field Officer KTM',
                'email' => 'field.ktm@microfinance.com',
                'password' => Hash::make('password'),
                'role_id' => $fieldOfficerRole->id,
                'branch_id' => 1,
                'employee_id' => 'EMP004',
                'phone' => '9876543213',
                'status' => 'active',
            ],
            [
                'name' => 'Accountant KTM',
                'email' => 'accountant.ktm@microfinance.com',
                'password' => Hash::make('password'),
                'role_id' => $accountantRole->id,
                'branch_id' => 1,
                'employee_id' => 'EMP005',
                'phone' => '9876543214',
                'status' => 'active',
            ],
        ];

        foreach ($users as $user) {
            // Check if user already exists before creating
            if (!User::where('email', $user['email'])->exists()) {
                User::create($user);
            }
        }
    }
}