<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Member;
use App\Models\Loan;
use App\Models\SavingsAccount;
use App\Models\Transaction;
use App\Models\LoanInstallment;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:access-dashboard');
    }
    
    public function index()
    {
        $user = Auth::user();
        
        // Get statistics based on user role
        $stats = $this->getStatsByRole($user);
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities($user);
        
        // Get chart data
        $chartData = $this->getChartData($user);
        
        return view('dashboard.index', compact('stats', 'recentActivities', 'chartData'));
    }
    
    private function getStatsByRole($user)
    {
        $query = collect();
        
        if ($user->isSuperAdmin()) {
            // Super admin sees all data
            // Calculate raw income (loan interest)
            $totalInterestIncome = LoanInstallment::where('status', 'paid')->sum('interest_amount');
            
            // Calculate total expenses
            $totalExpenses = Expense::where('status', 'approved')->sum('amount');
            
            // Calculate total savings for share bonus calculation
            $totalSavings = SavingsAccount::sum('balance');
            
            // Calculate share bonuses (based on member savings proportion of total raw income)
            $shareBonus = $totalInterestIncome * 0.7; // 70% of raw income goes to share bonuses
            
            // Calculate final balance (net income)
            $finalBalance = $totalInterestIncome - $shareBonus - $totalExpenses;
            
            // Get members with savings for bonus highlighting
            $membersWithSavings = Member::whereHas('savingsAccounts', function($q) {
                $q->where('balance', '>', 0);
            })->get();
            
            $stats = [
                'total_members' => Member::count(),
                'active_loans' => Loan::active()->count(),
                'total_savings' => $totalSavings,
                'overdue_installments' => LoanInstallment::overdue()->count(),
                'total_branches' => Branch::active()->count(),
                'monthly_disbursements' => Loan::whereMonth('disbursed_date', now()->month)->sum('approved_amount'),
                // Financial summary data
                'total_raw_income' => $totalInterestIncome,
                'total_share_bonus' => $shareBonus,
                'total_expenses' => $totalExpenses,
                'final_balance' => $finalBalance,
                'members_with_savings' => $membersWithSavings,
            ];
        } elseif ($user->isBranchManager() || $user->isFieldOfficer() || $user->isAccountant()) {
            // Branch-level users see branch data
            $branchId = $user->branch_id;
            
            // Calculate branch-specific raw income (loan interest)
            $totalInterestIncome = LoanInstallment::whereHas('loan', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })->where('status', 'paid')->sum('interest_amount');
            
            // Calculate branch-specific expenses
            $totalExpenses = Expense::where('branch_id', $branchId)
                ->where('status', 'approved')->sum('amount');
            
            // Calculate branch-specific total savings for share bonus calculation
            $totalSavings = SavingsAccount::where('branch_id', $branchId)->sum('balance');
            
            // Calculate branch-specific share bonuses
            $shareBonus = $totalInterestIncome * 0.7; // 70% of raw income goes to share bonuses
            
            // Calculate branch-specific final balance (net income)
            $finalBalance = $totalInterestIncome - $shareBonus - $totalExpenses;
            
            // Get branch-specific members with savings for bonus highlighting
            $membersWithSavings = Member::where('branch_id', $branchId)
                ->whereHas('savingsAccounts', function($q) {
                    $q->where('balance', '>', 0);
                })->get();
            
            $stats = [
                'total_members' => Member::where('branch_id', $branchId)->count(),
                'active_loans' => Loan::where('branch_id', $branchId)->active()->count(),
                'total_savings' => $totalSavings,
                'overdue_installments' => LoanInstallment::whereHas('loan', function($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                })->overdue()->count(),
                'monthly_disbursements' => Loan::where('branch_id', $branchId)
                    ->whereMonth('disbursed_date', now()->month)->sum('approved_amount'),
                // Financial summary data
                'total_raw_income' => $totalInterestIncome,
                'total_share_bonus' => $shareBonus,
                'total_expenses' => $totalExpenses,
                'final_balance' => $finalBalance,
                'members_with_savings' => $membersWithSavings,
            ];
        } else {
            // Members see their own data
            $memberId = $user->member_id;
            
            // Calculate member's savings
            $memberSavings = SavingsAccount::where('member_id', $memberId)->sum('balance');
            
            // Calculate total savings for proportion calculation
            $totalSavings = SavingsAccount::sum('balance');
            
            // Calculate total raw income
            $totalInterestIncome = LoanInstallment::where('status', 'paid')->sum('interest_amount');
            
            // Calculate member's share bonus based on savings proportion
            $shareBonus = 0;
            if ($totalSavings > 0) {
                $savingsProportion = $memberSavings / $totalSavings;
                $shareBonus = $totalInterestIncome * 0.7 * $savingsProportion; // 70% of raw income * member's proportion
            }
            
            $stats = [
                'active_loans' => Loan::where('member_id', $memberId)->active()->count(),
                'total_savings' => $memberSavings,
                'pending_installments' => LoanInstallment::whereHas('loan', function($q) use ($memberId) {
                    $q->where('member_id', $memberId);
                })->pending()->count(),
                // Member's financial data
                'share_bonus' => $shareBonus,
            ];
        }
        
        return $stats;
    }
    
    private function getRecentActivities($user)
    {
        $query = Transaction::with(['member', 'processedBy'])
            ->orderBy('created_at', 'desc')
            ->limit(10);
            
        if (!$user->isSuperAdmin()) {
            $query->where('branch_id', $user->branch_id);
        }
        
        return $query->get();
    }
    
    private function getChartData($user)
    {
        // Monthly loan disbursements for the last 6 months
        $loanData = [];
        $savingsData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            
            $loanQuery = Loan::whereYear('disbursed_date', $month->year)
                ->whereMonth('disbursed_date', $month->month);
            
            $savingsQuery = Transaction::where('transaction_type', 'deposit')
                ->whereYear('transaction_date', $month->year)
                ->whereMonth('transaction_date', $month->month);
                
            if (!$user->isSuperAdmin()) {
                $loanQuery->where('branch_id', $user->branch_id);
                $savingsQuery->where('branch_id', $user->branch_id);
            }
            
            $loanData[] = $loanQuery->sum('approved_amount') / 100000; // Convert to lakhs
            $savingsData[] = $savingsQuery->sum('amount') / 100000; // Convert to lakhs
        }
        
        return [
            'months' => ['बैशाख', 'जेठ', 'असार', 'साउन', 'भदौ', 'असोज'],
            'loan_data' => $loanData,
            'savings_data' => $savingsData,
        ];
    }
}