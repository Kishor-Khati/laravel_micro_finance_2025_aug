<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SavingsType;

class SavingsTypeSeeder extends Seeder
{
    public function run(): void
    {
        $savingsTypes = [
            [
                'name' => 'Regular Savings',
                'code' => 'REG001',
                'description' => 'Standard savings account with flexible deposit and withdrawal',
                'min_balance' => 500,
                'interest_rate' => 5.0,
                'withdrawal_limit_per_month' => 10,
                'withdrawal_limit_amount' => 50000,
                'is_mandatory' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Mandatory Savings',
                'code' => 'MAN001',
                'description' => 'Compulsory savings account for all members',
                'min_balance' => 1000,
                'interest_rate' => 6.0,
                'withdrawal_limit_per_month' => 2,
                'withdrawal_limit_amount' => 10000,
                'is_mandatory' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Fixed Deposit',
                'code' => 'FD001',
                'description' => 'Fixed term deposit with higher interest rate',
                'min_balance' => 10000,
                'interest_rate' => 8.0,
                'withdrawal_limit_per_month' => 0,
                'withdrawal_limit_amount' => 0,
                'is_mandatory' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Children Savings',
                'code' => 'CHD001',
                'description' => 'Special savings account for children education',
                'min_balance' => 100,
                'interest_rate' => 7.0,
                'withdrawal_limit_per_month' => 3,
                'withdrawal_limit_amount' => 5000,
                'is_mandatory' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Women Empowerment Savings',
                'code' => 'WOM001',
                'description' => 'Special savings account for women empowerment',
                'min_balance' => 200,
                'interest_rate' => 6.5,
                'withdrawal_limit_per_month' => 5,
                'withdrawal_limit_amount' => 25000,
                'is_mandatory' => false,
                'status' => 'active',
            ],
        ];

        foreach ($savingsTypes as $savingsType) {
            SavingsType::updateOrCreate(
                ['code' => $savingsType['code']], // Find by unique code
                $savingsType // Update or create with this data
            );
        }
    }
}