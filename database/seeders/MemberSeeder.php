<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\Branch;
use Carbon\Carbon;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        // Get branch IDs dynamically
        $kathmandu = Branch::where('code', 'KTM001')->first();
        $pokhara = Branch::where('code', 'PKR001')->first();
        $chitwan = Branch::where('code', 'CHT001')->first();
        $butwal = Branch::where('code', 'BTW001')->first();

        // Check if branches exist
        if (!$kathmandu || !$pokhara || !$chitwan || !$butwal) {
            $this->command->error('Required branches not found. Please run BranchSeeder first.');
            return;
        }

        $members = [
            [
                'member_number' => 'MEM00001',
                'first_name' => 'Ram',
                'middle_name' => 'Bahadur',
                'last_name' => 'Shrestha',
                'date_of_birth' => '1985-03-15',
                'gender' => 'male',
                'citizenship_number' => 'CIT001001',
                'phone' => '9841234567',
                'email' => 'ram.shrestha@email.com',
                'address' => 'Kathmandu-32, New Baneshwor',
                'occupation' => 'Farmer',
                'monthly_income' => 25000,
                'branch_id' => $kathmandu->id,
                'guardian_name' => 'Dhan Bahadur Shrestha',
                'guardian_phone' => '9841234568',
                'guardian_relation' => 'Father',
                'status' => 'active',
                'kyc_status' => 'verified',
                'membership_date' => Carbon::now()->subMonths(6),
            ],
            [
                'member_number' => 'MEM00002',
                'first_name' => 'Sita',
                'middle_name' => 'Devi',
                'last_name' => 'Gurung',
                'date_of_birth' => '1990-07-22',
                'gender' => 'female',
                'citizenship_number' => 'CIT002002',
                'phone' => '9841234568',
                'email' => 'sita.gurung@email.com',
                'address' => 'Pokhara-15, Lakeside',
                'occupation' => 'Shop Owner',
                'monthly_income' => 30000,
                'branch_id' => $pokhara->id,
                'guardian_name' => 'Man Bahadur Gurung',
                'guardian_phone' => '9841234569',
                'guardian_relation' => 'Husband',
                'status' => 'active',
                'kyc_status' => 'verified',
                'membership_date' => Carbon::now()->subMonths(4),
            ],
            [
                'member_number' => 'MEM00003',
                'first_name' => 'Hari',
                'middle_name' => 'Prasad',
                'last_name' => 'Poudel',
                'date_of_birth' => '1988-11-10',
                'gender' => 'male',
                'citizenship_number' => 'CIT003003',
                'phone' => '9841234569',
                'email' => 'hari.poudel@email.com',
                'address' => 'Chitwan-10, Bharatpur',
                'occupation' => 'Teacher',
                'monthly_income' => 35000,
                'branch_id' => $chitwan->id,
                'guardian_name' => 'Krishna Prasad Poudel',
                'guardian_phone' => '9841234570',
                'guardian_relation' => 'Father',
                'status' => 'kyc_pending',
                'kyc_status' => 'pending',
                'membership_date' => Carbon::now()->subMonths(1),
            ],
            [
                'member_number' => 'MEM00004',
                'first_name' => 'Maya',
                'middle_name' => 'Kumari',
                'last_name' => 'Tamang',
                'date_of_birth' => '1992-05-18',
                'gender' => 'female',
                'citizenship_number' => 'CIT004004',
                'phone' => '9841234570',
                'email' => 'maya.tamang@email.com',
                'address' => 'Butwal-12, Traffic Chowk',
                'occupation' => 'Tailor',
                'monthly_income' => 20000,
                'branch_id' => $butwal->id,
                'guardian_name' => 'Pemba Tamang',
                'guardian_phone' => '9841234571',
                'guardian_relation' => 'Husband',
                'status' => 'active',
                'kyc_status' => 'verified',
                'membership_date' => Carbon::now()->subMonths(8),
            ],
            [
                'member_number' => 'MEM00005',
                'first_name' => 'Bikash',
                'middle_name' => 'Kumar',
                'last_name' => 'Rai',
                'date_of_birth' => '1987-09-25',
                'gender' => 'male',
                'citizenship_number' => 'CIT005005',
                'phone' => '9841234571',
                'email' => 'bikash.rai@email.com',
                'address' => 'Kathmandu-25, Thamel',
                'occupation' => 'Driver',
                'monthly_income' => 28000,
                'branch_id' => $kathmandu->id,
                'guardian_name' => 'Dil Bahadur Rai',
                'guardian_phone' => '9841234572',
                'guardian_relation' => 'Father',
                'status' => 'active',
                'kyc_status' => 'verified',
                'membership_date' => Carbon::now()->subMonths(3),
            ],
        ];

        foreach ($members as $member) {
            Member::updateOrCreate(
                ['member_number' => $member['member_number']], // Find by unique member number
                $member // Update or create with this data
            );
        }
    }
}