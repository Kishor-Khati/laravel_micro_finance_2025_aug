<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LoanType;

class LoanTypeSeeder extends Seeder
{
    public function run(): void
    {
        $loanTypes = [
            [
                'name' => 'Agricultural Loan',
                'code' => 'AGR001',
                'description' => 'Loans for agricultural activities, farming equipment, and livestock',
                'min_amount' => 10000,
                'max_amount' => 500000,
                'interest_rate' => 12.0,
                'min_duration_months' => 6,
                'max_duration_months' => 36,
                'interest_type' => 'simple',
                'status' => 'active',
            ],
            [
                'name' => 'Business Loan',
                'code' => 'BUS001',
                'description' => 'Loans for small business development and expansion',
                'min_amount' => 25000,
                'max_amount' => 1000000,
                'interest_rate' => 15.0,
                'min_duration_months' => 12,
                'max_duration_months' => 60,
                'interest_type' => 'compound',
                'status' => 'active',
            ],
            [
                'name' => 'Education Loan',
                'code' => 'EDU001',
                'description' => 'Loans for educational purposes and skill development',
                'min_amount' => 5000,
                'max_amount' => 300000,
                'interest_rate' => 10.0,
                'min_duration_months' => 12,
                'max_duration_months' => 72,
                'interest_type' => 'simple',
                'status' => 'active',
            ],
            [
                'name' => 'Emergency Loan',
                'code' => 'EMR001',
                'description' => 'Quick loans for emergency situations',
                'min_amount' => 5000,
                'max_amount' => 100000,
                'interest_rate' => 18.0,
                'min_duration_months' => 3,
                'max_duration_months' => 24,
                'interest_type' => 'simple',
                'status' => 'active',
            ],
            [
                'name' => 'Housing Loan',
                'code' => 'HOU001',
                'description' => 'Loans for house construction and renovation',
                'min_amount' => 100000,
                'max_amount' => 2000000,
                'interest_rate' => 13.5,
                'min_duration_months' => 60,
                'max_duration_months' => 240,
                'interest_type' => 'compound',
                'status' => 'active',
            ],
        ];

        foreach ($loanTypes as $loanType) {
            LoanType::updateOrCreate(
                ['code' => $loanType['code']], // Find by unique code
                $loanType // Update or create with this data
            );
        }
    }
}