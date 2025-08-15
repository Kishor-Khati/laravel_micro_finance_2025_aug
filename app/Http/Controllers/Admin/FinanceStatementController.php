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
use App\Exports\FinanceStatementsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class FinanceStatementController extends Controller
{
    /**
     * Display the finance statements index page
     */
    public function index()
    {
        return view('admin.finance-statements.index');
    }
    
    /**
     * Generate a finance statement with share bonus calculations
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
        
        return view('admin.finance-statements.statement', compact('data', 'startDate', 'endDate'));
    }
    
    /**
     * Export finance statement to PDF
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
     * Export finance statement to PDF (helper method)
     */
    private function exportPdfData($data, $startDate, $endDate)
    {
        $pdf = PDF::loadView('admin.finance-statements.pdf', compact('data', 'startDate', 'endDate'));
        return $pdf->download('finance_statement_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.pdf');
    }
    
    /**
     * Export finance statement to Excel
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
     * Export finance statement to Excel (helper method)
     */
    private function exportExcelData($data, $startDate, $endDate)
    {
        return Excel::download(
             new FinanceStatementsExport($data, $startDate, $endDate),
             'finance_statement_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.xlsx'
         );
     }
    
    /**
     * Get all financial data for the given period
     */
    private function getFinancialData($startDate, $endDate, $branchId = null)
    {
        $loanQuery = Loan::whereBetween('created_at', [$startDate, $endDate]);
        $savingsQuery = SavingsAccount::whereBetween('created_at', [$startDate, $endDate]);
        $transactionQuery = Transaction::whereBetween('created_at', [$startDate, $endDate]);
        $expenseQuery = Expense::whereBetween('expense_date', [$startDate, $endDate]);
        
        if ($branchId) {
            $loanQuery->where('branch_id', $branchId);
            $savingsQuery->where('branch_id', $branchId);
            $transactionQuery->where('branch_id', $branchId);
            $expenseQuery->where('branch_id', $branchId);
        }
        
        // Calculate total raw income (loan interest earned)
        $totalRawIncome = $transactionQuery->clone()
            ->where('transaction_type', 'loan_payment')
            ->sum('interest_amount') ?? 0;
        
        // Get total expenses
        $totalExpenses = $expenseQuery->sum('amount') ?? 0;
        
        return [
            'total_raw_income' => $totalRawIncome,
            'total_expenses' => $totalExpenses,
            'loans' => [
                'total' => $loanQuery->count(),
                'amount' => $loanQuery->sum('amount'),
                'active' => $loanQuery->where('status', 'active')->count(),
            ],
            'savings' => [
                'total' => $savingsQuery->count(),
                'balance' => $savingsQuery->sum('balance'),
                'active' => $savingsQuery->where('status', 'active')->count(),
            ],
            'transactions' => [
                'total' => $transactionQuery->count(),
                'deposits' => $transactionQuery->where('transaction_type', 'deposit')->sum('amount'),
                'withdrawals' => $transactionQuery->where('transaction_type', 'withdrawal')->sum('amount'),
            ],
        ];
    }
    
    /**
     * Calculate share bonuses based on savings balances
     */
    private function calculateShareBonus($totalRawIncome, $startDate, $endDate, $branchId = null)
    {
        // Get all active savings accounts
        $savingsQuery = SavingsAccount::where('status', 'active');
        
        if ($branchId) {
            $savingsQuery->where('branch_id', $branchId);
        }
        
        $savingsAccounts = $savingsQuery->with('member')->get();
        
        // Calculate total savings balance
        $totalSavingsBalance = $savingsAccounts->sum('balance');
        
        // If no savings, return zero bonus
        if ($totalSavingsBalance <= 0) {
            return [
                'total_share_bonus' => 0,
                'members_with_savings' => collect(),
                'bonus_details' => [],
            ];
        }
        
        // Allocate 30% of raw income as share bonus
        $totalShareBonus = $totalRawIncome * 0.3;
        
        // Calculate individual share bonuses based on proportion of savings
        $bonusDetails = [];
        foreach ($savingsAccounts as $account) {
            $proportion = $account->balance / $totalSavingsBalance;
            $individualBonus = $totalShareBonus * $proportion;
            
            $bonusDetails[] = [
                'account_id' => $account->id,
                'account_number' => $account->account_number,
                'member_name' => $account->member->first_name . ' ' . $account->member->last_name,
                'savings_balance' => $account->balance,
                'proportion' => $proportion,
                'bonus_amount' => $individualBonus,
            ];
        }
        
        return [
            'total_share_bonus' => $totalShareBonus,
            'members_with_savings' => $savingsAccounts,
            'bonus_details' => $bonusDetails,
        ];
    }
}