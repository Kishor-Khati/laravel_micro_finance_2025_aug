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
use App\Exports\ShareBonusExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ShareBonusController extends Controller
{
    /**
     * Display the share bonus statements index page
     */
    public function index()
    {
        return view('admin.share-bonus.index');
    }
    
    /**
     * Generate a share bonus statement
     */
    public function generate(Request $request)
    {
        // Validate based on date option
        if ($request->date_option === 'all_time') {
            $request->validate([
                'branch_id' => 'nullable|exists:branches,id',
            ]);
            
            // Set start date to the earliest record and end date to today
            $startDate = Carbon::parse(Transaction::min('created_at') ?? now()->subYears(5))->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'branch_id' => 'nullable|exists:branches,id',
            ]);
            
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        }
        
        $branchId = $request->branch_id;
        
        // Get all financial data for the period
        $data = $this->getFinancialData($startDate, $endDate, $branchId);
        
        // Calculate share bonuses based on savings balances
        $shareBonus = $this->calculateShareBonus($data['total_raw_income'], $startDate, $endDate, $branchId);
        
        // Add share bonus data to the financial data
        $data['share_bonus'] = $shareBonus;
        
        // Calculate final balance
        $data['final_balance'] = $data['total_raw_income'] - $shareBonus['total_share_bonus'] - $data['total_expenses'];
        
        // Check if export is requested
        if ($request->has('export')) {
            if ($request->export === 'pdf') {
                return $this->exportPdfData($data, $startDate, $endDate);
            } elseif ($request->export === 'excel') {
                return $this->exportExcelData($data, $startDate, $endDate);
            }
        }
        
        return view('admin.share-bonus.statement', compact('data', 'startDate', 'endDate'));
    }
    
    /**
     * Export share bonus statement to PDF
     */
    public function exportPdf(Request $request)
    {
        // Validate based on date option
        if ($request->date_option === 'all_time') {
            $request->validate([
                'branch_id' => 'nullable|exists:branches,id',
            ]);
            
            // Set start date to the earliest record and end date to today
            $startDate = Carbon::parse(Transaction::min('created_at') ?? now()->subYears(5))->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'branch_id' => 'nullable|exists:branches,id',
            ]);
            
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        }
        
        $branchId = $request->branch_id;
        
        // Get all financial data for the period
        $data = $this->getFinancialData($startDate, $endDate, $branchId);
        
        // Calculate share bonuses based on savings balances
        $shareBonus = $this->calculateShareBonus($data['total_raw_income'], $startDate, $endDate, $branchId);
        
        // Add share bonus data to the financial data
        $data['share_bonus'] = $shareBonus;
        
        // Calculate final balance
        $data['final_balance'] = $data['total_raw_income'] - $shareBonus['total_share_bonus'] - $data['total_expenses'];
        
        return $this->exportPdfData($data, $startDate, $endDate);
    }
    
    /**
     * Export share bonus statement to PDF (helper method)
     */
    private function exportPdfData($data, $startDate, $endDate)
    {
        $pdf = PDF::loadView('admin.share-bonus.pdf', compact('data', 'startDate', 'endDate'));
        return $pdf->download('share_bonus_statement_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.pdf');
    }
    
    /**
     * Export share bonus statement to Excel
     */
    public function exportExcel(Request $request)
    {
        // Validate based on date option
        if ($request->date_option === 'all_time') {
            $request->validate([
                'branch_id' => 'nullable|exists:branches,id',
            ]);
            
            // Set start date to the earliest record and end date to today
            $startDate = Carbon::parse(Transaction::min('created_at') ?? now()->subYears(5))->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'branch_id' => 'nullable|exists:branches,id',
            ]);
            
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        }
        
        $branchId = $request->branch_id;
        
        // Get all financial data for the period
        $data = $this->getFinancialData($startDate, $endDate, $branchId);
        
        // Calculate share bonuses based on savings balances
        $shareBonus = $this->calculateShareBonus($data['total_raw_income'], $startDate, $endDate, $branchId);
        
        // Add share bonus data to the financial data
        $data['share_bonus'] = $shareBonus;
        
        // Calculate final balance
        $data['final_balance'] = $data['total_raw_income'] - $shareBonus['total_share_bonus'] - $data['total_expenses'];
        
        return $this->exportExcelData($data, $startDate, $endDate);
    }
    
    /**
     * Export share bonus statement to Excel (helper method)
     */
    private function exportExcelData($data, $startDate, $endDate)
    {
        return Excel::download(
            new ShareBonusExport($data, $startDate, $endDate),
            'share_bonus_statement_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.xlsx'
        );
    }
    
    /**
     * Get all financial data for a given period
     */
    private function getFinancialData($startDate, $endDate, $branchId = null)
    {
        $query = Transaction::whereBetween('created_at', [$startDate, $endDate]);
        $expenseQuery = Expense::whereBetween('expense_date', [$startDate, $endDate]);
        $loanQuery = Loan::whereBetween('created_at', [$startDate, $endDate]);
        $savingsQuery = SavingsAccount::query();
        
        if ($branchId) {
            $query->whereHas('member', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
            
            $expenseQuery->where('branch_id', $branchId);
            
            $loanQuery->whereHas('member', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
            
            $savingsQuery->whereHas('member', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }
        
        // Calculate raw income (loan interest)
        $totalRawIncome = $query->where('type', 'loan_payment')
            ->sum('interest_amount');
        
        // Get total expenses
        $totalExpenses = $expenseQuery->sum('amount');
        
        // Get loan data
        $loans = [
            'total_loans' => $loanQuery->count(),
            'total_amount' => $loanQuery->sum('amount'),
            'total_interest' => $loanQuery->sum('total_interest'),
            'active_loans' => $loanQuery->where('status', 'active')->count(),
            'active_amount' => $loanQuery->where('status', 'active')->sum('amount'),
        ];
        
        // Get savings data
        $savings = [
            'total_accounts' => $savingsQuery->count(),
            'total_balance' => $savingsQuery->sum('balance'),
            'active_accounts' => $savingsQuery->where('status', 'active')->count(),
            'active_balance' => $savingsQuery->where('status', 'active')->sum('balance'),
        ];
        
        // Get transaction data
        $transactions = [
            'deposits' => $query->where('type', 'deposit')->sum('amount'),
            'withdrawals' => $query->where('type', 'withdrawal')->sum('amount'),
            'loan_disbursements' => $query->where('type', 'loan_disbursement')->sum('amount'),
            'loan_payments' => $query->where('type', 'loan_payment')->sum('amount'),
            'total_transactions' => $query->count(),
        ];
        
        return [
            'total_raw_income' => $totalRawIncome,
            'total_expenses' => $totalExpenses,
            'loans' => $loans,
            'savings' => $savings,
            'transactions' => $transactions,
        ];
    }
    
    /**
     * Calculate share bonuses based on savings balances
     */
    private function calculateShareBonus($totalRawIncome, $startDate, $endDate, $branchId = null)
    {
        // Share bonus is 30% of raw income
        $totalShareBonus = $totalRawIncome * 0.3;
        
        // Get all active savings accounts
        $savingsQuery = SavingsAccount::where('status', 'active');
        
        if ($branchId) {
            $savingsQuery->whereHas('member', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }
        
        $savingsAccounts = $savingsQuery->with('member')->get();
        
        // Calculate total savings balance
        $totalSavingsBalance = $savingsAccounts->sum('balance');
        
        // Calculate share bonus for each member based on their proportion of total savings
        $memberBonuses = [];
        $totalDistributed = 0;
        
        foreach ($savingsAccounts as $account) {
            if ($totalSavingsBalance > 0) {
                $proportion = $account->balance / $totalSavingsBalance;
                $bonusAmount = $totalShareBonus * $proportion;
                
                $memberBonuses[] = [
                    'account_number' => $account->account_number,
                    'member_name' => $account->member->first_name . ' ' . $account->member->last_name,
                    'savings_balance' => $account->balance,
                    'proportion' => $proportion,
                    'bonus_amount' => $bonusAmount,
                ];
                
                $totalDistributed += $bonusAmount;
            }
        }
        
        return [
            'total_share_bonus' => $totalShareBonus,
            'total_distributed' => $totalDistributed,
            'member_bonuses' => $memberBonuses,
        ];
    }
}