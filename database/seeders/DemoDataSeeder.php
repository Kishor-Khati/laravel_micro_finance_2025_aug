<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\SavingsAccount;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\LoanInstallment;
use App\Models\Expense;
use App\Models\Branch;
use App\Models\LoanType;
use App\Models\SavingsType;
use App\Models\User;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing data
        $branches = Branch::all();
        $loanTypes = LoanType::all();
        $savingsTypes = SavingsType::all();
        $users = User::all();
        
        if ($branches->isEmpty() || $loanTypes->isEmpty() || $savingsTypes->isEmpty()) {
            $this->command->error('Please run basic seeders first (BranchSeeder, LoanTypeSeeder, SavingsTypeSeeder)');
            return;
        }

        // Create additional members if needed
        $this->createAdditionalMembers($branches);
        
        // Create savings accounts for all members with varied balances for share bonus demo
        $this->createSavingsAccounts($branches, $savingsTypes, $users);
        
        // Create loans for some members
        $this->createLoans($branches, $loanTypes, $users);
        
        // Create loan installments with higher payment rates for share bonus income
        $this->createLoanInstallments();
        
        // Create various transactions
        $this->createTransactions($branches, $users);
        
        // Create expenses
        $this->createExpenses($branches, $users);
        
        // Display share bonus calculation demo
        $this->displayShareBonusDemo();
        
        $this->command->info('Demo data seeded successfully!');
    }

    private function createAdditionalMembers($branches)
    {
        $currentMemberCount = Member::count();
        $targetMemberCount = 20;
        
        if ($currentMemberCount >= $targetMemberCount) {
            return;
        }
        
        $membersToCreate = $targetMemberCount - $currentMemberCount;
        
        $nepaliNames = [
            ['राम', 'श्रेष्ठ'], ['सीता', 'गुरुङ'], ['हरि', 'तामाङ'], ['गीता', 'मगर'],
            ['कृष्ण', 'थापा'], ['लक्ष्मी', 'पौडेल'], ['शिव', 'अधिकारी'], ['पार्वती', 'खत्री'],
            ['विष्णु', 'दाहाल'], ['सरस्वती', 'कार्की'], ['गणेश', 'बस्नेत'], ['दुर्गा', 'रेग्मी'],
            ['इन्द्र', 'भट्टराई'], ['कमला', 'शर्मा'], ['सुरेश', 'न्यौपाने'], ['मीरा', 'जोशी']
        ];
        
        for ($i = 0; $i < $membersToCreate; $i++) {
            $nameIndex = $i % count($nepaliNames);
            $branch = $branches->random();
            
            Member::create([
                'member_number' => 'MEM' . str_pad(Member::count() + 1, 6, '0', STR_PAD_LEFT),
                'first_name' => $nepaliNames[$nameIndex][0],
                'last_name' => $nepaliNames[$nameIndex][1],
                'email' => 'member' . ($currentMemberCount + $i + 1) . '@example.com',
                'phone' => '98' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                'address' => 'वडा नं. ' . rand(1, 15) . ', ' . $branch->name,
                'date_of_birth' => Carbon::now()->subYears(rand(25, 65))->format('Y-m-d'),
                'gender' => rand(0, 1) ? 'male' : 'female',
                'occupation' => ['किसान', 'व्यापारी', 'शिक्षक', 'कर्मचारी', 'गृहिणी'][rand(0, 4)],
                'citizenship_number' => rand(1000, 9999) . '-' . rand(1000, 9999) . '-' . rand(1000, 9999),
                'branch_id' => $branch->id,
                'status' => 'active',
                'membership_date' => Carbon::now()->subDays(rand(30, 365))->format('Y-m-d'),
            ]);
        }
    }

    private function createSavingsAccounts($branches, $savingsTypes, $users)
    {
        $members = Member::all();
        
        // Create varied savings balances to demonstrate share bonus distribution
        $balanceRanges = [
            [10000, 50000],   // Small savers
            [50000, 150000],  // Medium savers
            [150000, 500000], // Large savers
            [500000, 1000000] // Premium savers
        ];
        
        foreach ($members as $index => $member) {
            // Each member gets 1-2 savings accounts
            $accountCount = rand(1, 2);
            
            for ($i = 0; $i < $accountCount; $i++) {
                $savingsType = $savingsTypes->random();
                
                // Create varied balances for meaningful share bonus demo
                $rangeIndex = $index % count($balanceRanges);
                $range = $balanceRanges[$rangeIndex];
                $balance = rand($range[0], $range[1]);
                
                $openedDate = Carbon::now()->subDays(rand(1, 300));
                
                $account = SavingsAccount::create([
                    'account_number' => 'SA' . str_pad(SavingsAccount::count() + 1, 8, '0', STR_PAD_LEFT),
                    'member_id' => $member->id,
                    'branch_id' => $member->branch_id,
                    'savings_type_id' => $savingsType->id,
                    'balance' => $balance,
                    'interest_earned' => rand(100, 1000),
                    'opened_date' => $openedDate->format('Y-m-d'),
                    'status' => 'active',
                ]);
                
                // Create initial deposit transaction
                Transaction::create([
                    'transaction_number' => 'TXN' . str_pad(Transaction::count() + 1, 8, '0', STR_PAD_LEFT),
                    'member_id' => $member->id,
                    'branch_id' => $member->branch_id,
                    'transaction_type' => 'deposit',
                    'amount' => $balance,
                    'balance_before' => 0,
                    'balance_after' => $balance,
                    'reference_type' => 'savings_account',
                    'reference_id' => $account->id,
                    'description' => "Initial deposit for account #{$account->account_number}",
                    'processed_by' => $users->random()->id,
                    'transaction_date' => $openedDate,
                ]);
            }
        }
    }

    private function createLoans($branches, $loanTypes, $users)
    {
        $members = Member::whereHas('savingsAccounts')->get();
        $loanCount = min(15, $members->count()); // Create loans for up to 15 members
        
        $selectedMembers = $members->random($loanCount);
        
        foreach ($selectedMembers as $member) {
            $loanType = $loanTypes->random();
            $requestedAmount = rand(50000, 1000000);
            $approvedAmount = $requestedAmount * (rand(80, 100) / 100); // 80-100% approval
            $interestRate = $loanType->interest_rate + rand(-1, 2); // Slight variation
            $durationMonths = [6, 12, 18, 24, 36][rand(0, 4)];
            $applicationDate = Carbon::now()->subDays(rand(30, 180));
            $disbursedDate = $applicationDate->copy()->addDays(rand(7, 30));
            
            $loan = Loan::create([
                'loan_number' => 'LN' . str_pad(Loan::count() + 1, 6, '0', STR_PAD_LEFT),
                'member_id' => $member->id,
                'branch_id' => $member->branch_id,
                'loan_type_id' => $loanType->id,
                'requested_amount' => $requestedAmount,
                'approved_amount' => $approvedAmount,
                'interest_rate' => $interestRate,
                'duration_months' => $durationMonths,
                'monthly_installment' => $this->calculateMonthlyInstallment($approvedAmount, $interestRate, $durationMonths),
                'purpose' => ['व्यापार विस्तार', 'कृषि', 'शिक्षा', 'घर निर्माण', 'स्वास्थ्य'][rand(0, 4)],
                'collateral' => 'जग्गाको लालपुर्जा',
                'application_date' => $applicationDate->format('Y-m-d'),
                'approved_date' => $applicationDate->copy()->addDays(rand(3, 15))->format('Y-m-d'),
                'disbursed_date' => $disbursedDate->format('Y-m-d'),
                'status' => 'disbursed',
                'approved_by' => $users->random()->id,
            ]);
            
            // Create loan disbursement transaction
            Transaction::create([
                'transaction_number' => 'TXN' . str_pad(Transaction::count() + 1, 8, '0', STR_PAD_LEFT),
                'member_id' => $member->id,
                'branch_id' => $member->branch_id,
                'transaction_type' => 'loan_disbursement',
                'amount' => $approvedAmount,
                'balance_before' => 0,
                'balance_after' => $approvedAmount,
                'reference_type' => 'loan',
                'reference_id' => $loan->id,
                'description' => "Loan disbursement for loan #{$loan->loan_number}",
                'processed_by' => $users->random()->id,
                'transaction_date' => $disbursedDate,
            ]);
        }
    }

    private function createLoanInstallments()
    {
        $loans = Loan::where('status', 'disbursed')->get();
        
        foreach ($loans as $loan) {
            $disbursedDate = Carbon::parse($loan->disbursed_date);
            $monthlyInstallment = $loan->monthly_installment;
            
            // Create installments for the loan duration
            for ($month = 1; $month <= $loan->duration_months; $month++) {
                $dueDate = $disbursedDate->copy()->addMonths($month);
                $isPaid = $dueDate->isPast() && rand(0, 100) < 98; // 98% payment rate for excellent income
                
                // Calculate proper installment amounts
                $principalAmount = $loan->approved_amount / $loan->duration_months;
                $monthlyInterestRate = $loan->interest_rate / 100 / 12;
                $interestAmount = $loan->approved_amount * $monthlyInterestRate;
                $totalAmount = $principalAmount + $interestAmount;
                
                $outstandingAmount = $isPaid ? 0 : $totalAmount;
                
                $installment = LoanInstallment::create([
                    'loan_id' => $loan->id,
                    'installment_number' => $month,
                    'due_date' => $dueDate->format('Y-m-d'),
                    'principal_amount' => $principalAmount,
                    'interest_amount' => $interestAmount,
                    'total_amount' => $totalAmount,
                    'paid_amount' => $isPaid ? $totalAmount : 0,
                    'outstanding_amount' => $outstandingAmount,
                    'paid_date' => $isPaid ? $dueDate->addDays(rand(-5, 10))->format('Y-m-d') : null,
                    'status' => $isPaid ? 'paid' : ($dueDate->isPast() ? 'overdue' : 'pending'),
                ]);
                
                // Create payment transaction if paid
                if ($isPaid) {
                    Transaction::create([
                        'transaction_number' => 'TXN' . str_pad(Transaction::count() + 1, 8, '0', STR_PAD_LEFT),
                        'member_id' => $loan->member_id,
                        'branch_id' => $loan->branch_id,
                        'transaction_type' => 'loan_payment',
                        'amount' => $totalAmount,
                        'interest_amount' => $interestAmount,
                        'balance_before' => 0,
                        'balance_after' => 0,
                        'reference_type' => 'loan_installment',
                        'reference_id' => $installment->id,
                        'description' => "Loan payment for installment #{$month} of loan #{$loan->loan_number}",
                        'processed_by' => User::inRandomOrder()->first()->id,
                        'transaction_date' => $installment->paid_date,
                    ]);
                }
            }
        }
    }

    private function createTransactions($branches, $users)
    {
        $savingsAccounts = SavingsAccount::with('member')->get();
        
        // Create additional deposit and withdrawal transactions
        foreach ($savingsAccounts as $account) {
            $transactionCount = rand(3, 8);
            
            for ($i = 0; $i < $transactionCount; $i++) {
                $isDeposit = rand(0, 100) < 70; // 70% deposits, 30% withdrawals
                $amount = rand(1000, 50000);
                $transactionDate = Carbon::now()->subDays(rand(1, 90));
                
                if (!$isDeposit && $amount > $account->balance) {
                    continue; // Skip withdrawal if insufficient balance
                }
                
                $balanceBefore = $account->balance;
                $balanceAfter = $isDeposit ? $balanceBefore + $amount : $balanceBefore - $amount;
                
                Transaction::create([
                    'transaction_number' => 'TXN' . str_pad(Transaction::count() + 1, 8, '0', STR_PAD_LEFT),
                    'member_id' => $account->member_id,
                    'branch_id' => $account->branch_id,
                    'transaction_type' => $isDeposit ? 'deposit' : 'withdrawal',
                    'amount' => $amount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'reference_type' => 'savings_account',
                    'reference_id' => $account->id,
                    'description' => $isDeposit ? 'नगद जम्मा' : 'नगद निकासी',
                    'processed_by' => $users->random()->id,
                    'transaction_date' => $transactionDate,
                ]);
                
                // Update account balance
                $account->update(['balance' => $balanceAfter]);
            }
        }
    }

    private function createExpenses($branches, $users)
    {
        // First calculate total expected raw income to ensure expenses are reasonable
        $totalExpectedIncome = $this->calculateExpectedRawIncome();
        
        // Ensure total expenses are only 60% of raw income for healthy profit margin
        $maxTotalExpenses = $totalExpectedIncome * 0.6;
        $expensesPerBranch = $maxTotalExpenses / $branches->count();
        
        $expenseTypes = [
            'कार्यालय भाडा',
            'कर्मचारी तलब',
            'बिजुली बिल',
            'फोन बिल',
            'कार्यालय सामग्री',
            'यातायात खर्च',
            'बैठक भत्ता',
            'मर्मत खर्च',
            'बीमा प्रिमियम',
            'कानुनी सल्लाह'
        ];
        
        foreach ($branches as $branch) {
            $expenseCount = rand(4, 8); // Reduced number of expenses
            $remainingBudget = $expensesPerBranch;
            
            for ($i = 0; $i < $expenseCount; $i++) {
                // Distribute remaining budget across remaining expenses
                $maxAmount = $remainingBudget / ($expenseCount - $i);
                $minAmount = min(2000, $maxAmount * 0.3);
                $amount = rand($minAmount, min($maxAmount, 25000)); // Cap individual expenses
                
                $expenseDate = Carbon::now()->subDays(rand(1, 180));
                
                Expense::create([
                    'expense_number' => 'EXP' . str_pad(Expense::count() + 1, 6, '0', STR_PAD_LEFT),
                    'branch_id' => $branch->id,
                    'category' => $expenseTypes[rand(0, count($expenseTypes) - 1)],
                    'title' => $expenseTypes[rand(0, count($expenseTypes) - 1)],
                    'description' => 'मासिक ' . $expenseTypes[rand(0, count($expenseTypes) - 1)],
                    'amount' => $amount,
                    'expense_date' => $expenseDate->format('Y-m-d'),
                    'status' => ['pending', 'approved', 'approved', 'approved'][rand(0, 3)], // 75% approved
                    'requested_by' => $users->random()->id,
                    'approved_by' => rand(0, 100) < 75 ? $users->random()->id : null,
                ]);
                
                $remainingBudget -= $amount;
            }
        }
    }
    
    private function calculateExpectedRawIncome()
    {
        // Calculate expected raw income from existing loan installments
        $totalInterestIncome = LoanInstallment::where('status', 'paid')->sum('interest_amount');
        
        // If no installments exist yet, estimate based on loan amounts and rates
        if ($totalInterestIncome == 0) {
            $loans = Loan::where('status', 'disbursed')->get();
            $estimatedIncome = 0;
            
            foreach ($loans as $loan) {
                // Estimate 80% of total interest will be collected
                $totalInterest = ($loan->approved_amount * $loan->interest_rate / 100) * ($loan->duration_months / 12);
                $estimatedIncome += $totalInterest * 0.8;
            }
            
            return max($estimatedIncome, 500000); // Minimum expected income
        }
        
        return $totalInterestIncome;
    }

    private function calculateMonthlyInstallment($principal, $annualRate, $months)
    {
        $monthlyRate = $annualRate / 100 / 12;
        if ($monthlyRate == 0) {
            return $principal / $months;
        }
        
        return $principal * ($monthlyRate * pow(1 + $monthlyRate, $months)) / (pow(1 + $monthlyRate, $months) - 1);
    }

    private function displayShareBonusDemo()
    {
        // Calculate financial metrics according to correct logic
        $totalRawIncome = LoanInstallment::where('status', 'paid')->sum('interest_amount');
        $totalExpenses = Expense::where('status', 'approved')->sum('amount');
        $netIncome = $totalRawIncome - $totalExpenses; // Net Income = Raw Income - Expenses
        $totalShareBonus = $netIncome * 0.3; // Share Bonus = 30% of Net Income
        $availableBalance = $netIncome - $totalShareBonus; // Available Balance = Net Income - Share Bonus
        
        // Get savings accounts for distribution
        $savingsAccounts = SavingsAccount::where('status', 'active')->with('member')->get();
        $totalSavingsBalance = $savingsAccounts->sum('balance');
        
        $this->command->info('\n=== REALISTIC FINANCIAL DEMO DATA SUMMARY ===');
        $this->command->info('Raw Income (Loan Interest): Rs. ' . number_format($totalRawIncome, 2));
        $this->command->info('Total Expenses: Rs. ' . number_format($totalExpenses, 2));
        $this->command->info('Net Income (Raw - Expenses): Rs. ' . number_format($netIncome, 2));
        $this->command->info('Share Bonus (30% of Net): Rs. ' . number_format($totalShareBonus, 2));
        $this->command->info('Available Balance (Net - Share): Rs. ' . number_format($availableBalance, 2));
        $this->command->info('Total Savings Balance: Rs. ' . number_format($totalSavingsBalance, 2));
        $this->command->info('Number of Active Savings Accounts: ' . $savingsAccounts->count());
        
        $this->command->info('\n=== TOP 5 SHARE BONUS RECIPIENTS ===');
        $memberBonuses = [];
        
        foreach ($savingsAccounts->take(5) as $account) {
            if ($totalSavingsBalance > 0) {
                $proportion = $account->balance / $totalSavingsBalance;
                $bonusAmount = $totalShareBonus * $proportion;
                
                $memberBonuses[] = [
                    'name' => $account->member->first_name . ' ' . $account->member->last_name,
                    'account' => $account->account_number,
                    'balance' => $account->balance,
                    'proportion' => $proportion * 100,
                    'bonus' => $bonusAmount
                ];
            }
        }
        
        foreach ($memberBonuses as $bonus) {
            $this->command->info(sprintf(
                '%s (%s): Balance Rs. %s (%.2f%%) → Bonus Rs. %s',
                $bonus['name'],
                $bonus['account'],
                number_format($bonus['balance'], 2),
                $bonus['proportion'],
                number_format($bonus['bonus'], 2)
            ));
        }
        
        $this->command->info('\nShare bonus data is now ready for testing in the application!');
    }
}