<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Member;
use App\Models\Loan;
use App\Models\SavingsAccount;
use App\Models\Transaction;
use App\Models\Expense;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }
    
    public function documentation()
    {
        return view('admin.reports.documentation');
    }

    public function financial()
    {
        $data = [
            'total_loans' => Loan::sum('approved_amount'),
            'active_loans' => Loan::where('status', 'active')->sum('approved_amount'),
            'total_savings' => SavingsAccount::sum('balance'),
            'total_deposits' => Transaction::where('transaction_type', 'deposit')->sum('amount'),
            'total_withdrawals' => Transaction::where('transaction_type', 'withdrawal')->sum('amount'),
            'total_expenses' => Expense::sum('amount'),
            'monthly_loans' => Loan::whereMonth('created_at', date('m'))->sum('approved_amount'),
            'monthly_savings' => Transaction::where('transaction_type', 'deposit')->whereMonth('created_at', date('m'))->sum('amount'),
            'monthly_expenses' => Expense::whereMonth('expense_date', date('m'))->sum('amount'),
        ];

        // Monthly trends for charts
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'loans' => Loan::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->sum('approved_amount'),
                'deposits' => Transaction::where('transaction_type', 'deposit')->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->sum('amount'),
                'expenses' => Expense::whereYear('expense_date', $date->year)->whereMonth('expense_date', $date->month)->sum('amount'),
            ];
        }

        return view('admin.reports.financial', compact('data', 'monthlyData'));
    }

    public function members()
    {
        $data = [
            'total_members' => Member::count(),
            'active_members' => Member::whereHas('savingsAccounts', function($q) {
                $q->where('status', 'active');
            })->count(),
            'members_with_loans' => Member::whereHas('loans')->count(),
            'members_with_savings' => Member::whereHas('savingsAccounts')->count(),
            'new_members_this_month' => Member::whereMonth('created_at', date('m'))->count(),
            'members_by_branch' => Member::with('branch')->get()->groupBy('branch.name'),
            'members_by_gender' => Member::select('gender', DB::raw('count(*) as count'))->groupBy('gender')->get(),
            'avg_member_age' => Member::selectRaw('AVG(YEAR(CURDATE()) - YEAR(date_of_birth)) as avg_age')->first()->avg_age,
        ];

        // Member registration trends
        $registrationTrends = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $registrationTrends[] = [
                'month' => $date->format('M Y'),
                'count' => Member::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(),
            ];
        }

        return view('admin.reports.members', compact('data', 'registrationTrends'));
    }

    public function loans()
    {
        $data = [
            'total_loans' => Loan::count(),
            'pending_loans' => Loan::where('status', 'pending')->count(),
            'active_loans' => Loan::where('status', 'active')->count(),
            'closed_loans' => Loan::where('status', 'closed')->count(),
            'defaulted_loans' => Loan::where('status', 'defaulted')->count(),
            'total_loan_amount' => Loan::sum('approved_amount'),
            'avg_loan_amount' => Loan::avg('approved_amount'),
            'loans_by_type' => Loan::with('loanType')->get()->groupBy('loanType.name'),
            'loans_by_status' => Loan::select('status', DB::raw('count(*) as count'), DB::raw('sum(approved_amount) as total_amount'))
                                   ->groupBy('status')->get(),
        ];

        // Loan disbursement trends
        $disbursementTrends = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $disbursementTrends[] = [
                'month' => $date->format('M Y'),
                'count' => Loan::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(),
                'amount' => Loan::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->sum('approved_amount'),
            ];
        }

        return view('admin.reports.loans', compact('data', 'disbursementTrends'));
    }

    public function branches()
    {
        $branches = Branch::withCount(['users', 'members'])
                         ->with(['members.loans', 'members.savingsAccounts'])
                         ->get();

        $branchData = $branches->map(function ($branch) {
            return [
                'branch' => $branch,
                'total_loans' => $branch->members->flatMap->loans->sum('approved_amount'),
                'active_loans' => $branch->members->flatMap->loans->where('status', 'active')->count(),
                'total_savings' => $branch->members->flatMap->savingsAccounts->sum('balance'),
                'active_savings_accounts' => $branch->members->flatMap->savingsAccounts->where('status', 'active')->count(),
            ];
        });

        return view('admin.reports.branches', compact('branchData'));
    }

    public function transactions()
    {
        $data = [
            'total_transactions' => Transaction::count(),
            'total_deposits' => Transaction::where('transaction_type', 'deposit')->count(),
            'total_withdrawals' => Transaction::where('transaction_type', 'withdrawal')->count(),
            'deposit_amount' => Transaction::where('transaction_type', 'deposit')->sum('amount'),
            'withdrawal_amount' => Transaction::where('transaction_type', 'withdrawal')->sum('amount'),
            'avg_transaction_amount' => Transaction::avg('amount'),
            'today_transactions' => Transaction::whereDate('created_at', today())->count(),
            'monthly_transactions' => Transaction::whereMonth('created_at', date('m'))->count(),
        ];

        // Daily transaction trends for current month
        $dailyTrends = [];
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        for ($date = $startOfMonth->copy(); $date <= $endOfMonth; $date->addDay()) {
            $dailyTrends[] = [
                'date' => $date->format('M d'),
                'deposits' => Transaction::where('transaction_type', 'deposit')->whereDate('created_at', $date)->sum('amount'),
            'withdrawals' => Transaction::where('transaction_type', 'withdrawal')->whereDate('created_at', $date)->sum('amount'),
                'count' => Transaction::whereDate('created_at', $date)->count(),
            ];
        }

        return view('admin.reports.transactions', compact('data', 'dailyTrends'));
    }

    public function summary()
    {
        $summary = [
            'members' => [
                'total' => Member::count(),
                'this_month' => Member::whereMonth('created_at', date('m'))->count(),
                'growth' => $this->calculateGrowth(
                    Member::whereMonth('created_at', date('m'))->count(),
                    Member::whereMonth('created_at', date('m', strtotime('-1 month')))->count()
                ),
            ],
            'loans' => [
                'total_count' => Loan::count(),
                'total_amount' => Loan::sum('approved_amount'),
                'active_count' => Loan::where('status', 'active')->count(),
                'this_month' => Loan::whereMonth('created_at', date('m'))->sum('approved_amount'),
                'growth' => (
                    Loan::whereMonth('created_at', date('m'))->sum('approved_amount') -
                    Loan::whereMonth('created_at', date('m', strtotime('-1 month')))->sum('approved_amount')
                ),
            ],
            'savings' => [
                'total_accounts' => SavingsAccount::count(),
                'total_balance' => SavingsAccount::sum('balance'),
                'active_accounts' => SavingsAccount::where('status', 'active')->count(),
                'this_month' => Transaction::where('transaction_type', 'deposit')->whereMonth('created_at', date('m'))->sum('amount'),
            'growth' => (
                Transaction::where('transaction_type', 'deposit')->whereMonth('created_at', date('m'))->sum('amount') -
                Transaction::where('transaction_type', 'deposit')->whereMonth('created_at', date('m', strtotime('-1 month')))->sum('amount')
            ),
            ],
            'transactions' => [
                'total_count' => Transaction::count(),
                'this_month' => Transaction::whereMonth('created_at', date('m'))->count(),
                'deposit_amount' => Transaction::where('transaction_type', 'deposit')->sum('amount'),
            'withdrawal_amount' => Transaction::where('transaction_type', 'withdrawal')->sum('amount'),
            ],
        ];

        return view('admin.reports.summary', compact('summary'));
    }

    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return round((($current - $previous) / $previous) * 100, 2);
    }
}