<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
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
        $memberRole = Role::where('slug', 'member')->first();
        
        if (!$superAdminRole || !$branchManagerRole || !$fieldOfficerRole || !$accountantRole || !$memberRole) {
            $this->command->info('Please run the RoleSeeder first!');
            return;
        }
        
        // Get branch IDs dynamically
        $kathmanduBranch = Branch::where('code', 'KTM001')->first();
        $pokharaBranch = Branch::where('code', 'PKR001')->first();
        
        if (!$kathmanduBranch || !$pokharaBranch) {
            $this->command->info('Please run the BranchSeeder first!');
            return;
        }
        
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
                'branch_id' => $kathmanduBranch->id,
                'employee_id' => 'EMP002',
                'phone' => '9876543211',
                'status' => 'active',
            ],
            [
                'name' => 'Pokhara Manager',
                'email' => 'manager.pkr@microfinance.com',
                'password' => Hash::make('password'),
                'role_id' => $branchManagerRole->id,
                'branch_id' => $pokharaBranch->id,
                'employee_id' => 'EMP003',
                'phone' => '9876543212',
                'status' => 'active',
            ],
            [
                'name' => 'Field Officer KTM',
                'email' => 'field.ktm@microfinance.com',
                'password' => Hash::make('password'),
                'role_id' => $fieldOfficerRole->id,
                'branch_id' => $kathmanduBranch->id,
                'employee_id' => 'EMP004',
                'phone' => '9876543213',
                'status' => 'active',
            ],
            [
                'name' => 'Accountant KTM',
                'email' => 'accountant.ktm@microfinance.com',
                'password' => Hash::make('password'),
                'role_id' => $accountantRole->id,
                'branch_id' => $kathmanduBranch->id,
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