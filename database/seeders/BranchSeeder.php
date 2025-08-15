<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'name' => 'Kathmandu Main Branch',
                'code' => 'KTM001',
                'address' => 'New Baneshwor, Kathmandu',
                'phone' => '01-4123456',
                'email' => 'kathmandu@microfinance.com',
                'manager_name' => 'Ram Bahadur Shrestha',
                'status' => 'active',
            ],
            [
                'name' => 'Pokhara Branch',
                'code' => 'PKR001',
                'address' => 'Lakeside, Pokhara',
                'phone' => '061-456789',
                'email' => 'pokhara@microfinance.com',
                'manager_name' => 'Sita Devi Gurung',
                'status' => 'active',
            ],
            [
                'name' => 'Chitwan Branch',
                'code' => 'CHT001',
                'address' => 'Bharatpur, Chitwan',
                'phone' => '056-123456',
                'email' => 'chitwan@microfinance.com',
                'manager_name' => 'Hari Prasad Poudel',
                'status' => 'active',
            ],
            [
                'name' => 'Butwal Branch',
                'code' => 'BTW001',
                'address' => 'Traffic Chowk, Butwal',
                'phone' => '071-789012',
                'email' => 'butwal@microfinance.com',
                'manager_name' => 'Krishna Maya Thapa',
                'status' => 'active',
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}