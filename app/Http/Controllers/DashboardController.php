<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Member;
use App\Models\Loan;
use App\Models\SavingsAccount;
use App\Models\Transaction;
use App\Models\LoanInstallment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
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
            $stats = [
                'total_members' => Member::count(),
                'active_loans' => Loan::active()->count(),
                'total_savings' => SavingsAccount::sum('balance'),
                'overdue_installments' => LoanInstallment::overdue()->count(),
                'total_branches' => Branch::active()->count(),
                'monthly_disbursements' => Loan::whereMonth('disbursed_date', now()->month)->sum('approved_amount'),
            ];
        } elseif ($user->isBranchManager() || $user->isFieldOfficer() || $user->isAccountant()) {
            // Branch-level users see branch data
            $branchId = $user->branch_id;
            $stats = [
                'total_members' => Member::where('branch_id', $branchId)->count(),
                'active_loans' => Loan::where('branch_id', $branchId)->active()->count(),
                'total_savings' => SavingsAccount::where('branch_id', $branchId)->sum('balance'),
                'overdue_installments' => LoanInstallment::whereHas('loan', function($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                })->overdue()->count(),
                'monthly_disbursements' => Loan::where('branch_id', $branchId)
                    ->whereMonth('disbursed_date', now()->month)->sum('approved_amount'),
            ];
        } else {
            // Members see their own data
            $stats = [
                'active_loans' => Loan::where('member_id', $user->id)->active()->count(),
                'total_savings' => SavingsAccount::where('member_id', $user->id)->sum('balance'),
                'pending_installments' => LoanInstallment::whereHas('loan', function($q) use ($user) {
                    $q->where('member_id', $user->id);
                })->pending()->count(),
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