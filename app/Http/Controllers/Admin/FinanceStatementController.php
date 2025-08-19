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
use App\Models\ShareBonus;
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
                'end_date' => 'required|date',
                'branch_id' => 'nullable|exists:branches,id',
            ]);
            
            // Parse English dates directly
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        }
        
        $branchId = $request->branch_id;
        
        // Get all financial data for the period
        $data = $this->getFinancialData($startDate, $endDate, $branchId);
        
        // Calculate net income (raw income - expenses)
        $data['net_income'] = $data['total_raw_income'] - $data['total_expenses'];
        
        // Calculate share bonuses based on net income
        $shareBonus = $this->calculateShareBonus($data['net_income'], $startDate, $endDate, $branchId);
        
        // Add share bonus data to the financial data
        $data['share_bonus'] = $shareBonus;
        
        // Final balance remains the same as net income (share bonus is distributed from net income, not deducted)
        $data['final_balance'] = $data['net_income'];
        
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
                'start_date' => 'required|string',
                'end_date' => 'required|string',
                'branch_id' => 'nullable|exists:branches,id',
            ]);
            
            // Use AD dates for database processing (from hidden _ad fields)
            $startDateAD = $request->start_date_ad ?? $request->start_date;
            $endDateAD = $request->end_date_ad ?? $request->end_date;
            
            $startDate = Carbon::parse($startDateAD)->startOfDay();
            $endDate = Carbon::parse($endDateAD)->endOfDay();
        }
        
        $branchId = $request->branch_id;
        
        // Get all financial data for the period
        $data = $this->getFinancialData($startDate, $endDate, $branchId);
        
        // Calculate net income (raw income - expenses)
        $data['net_income'] = $data['total_raw_income'] - $data['total_expenses'];
        
        // Calculate share bonuses based on net income
        $shareBonus = $this->calculateShareBonus($data['net_income'], $startDate, $endDate, $branchId);
        
        // Add share bonus data to the financial data
        $data['share_bonus'] = $shareBonus;
        
        // Final balance remains the same as net income (share bonus is distributed from net income, not deducted)
        $data['final_balance'] = $data['net_income'];
        
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
                'start_date' => ['required', 'date'],
                'end_date' => ['required', 'date'],
                'branch_id' => 'nullable|exists:branches,id',
            ]);
            
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        }
        
        $branchId = $request->branch_id;
        
        // Get all financial data for the period
        $data = $this->getFinancialData($startDate, $endDate, $branchId);
        
        // Calculate net income (raw income - expenses)
        $data['net_income'] = $data['total_raw_income'] - $data['total_expenses'];
        
        // Calculate share bonuses based on net income
        $shareBonus = $this->calculateShareBonus($data['net_income'], $startDate, $endDate, $branchId);
        
        // Add share bonus data to the financial data
        $data['share_bonus'] = $shareBonus;
        
        // Final balance remains the same as net income (share bonus is distributed from net income, not deducted)
        $data['final_balance'] = $data['net_income'];
        
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
                'amount' => $loanQuery->sum('approved_amount'),
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
        // Get stored share bonus entries within the date range
        $shareBonusQuery = ShareBonus::approved()
            ->whereBetween('date', [$startDate, $endDate]);
        
        if ($branchId) {
            $shareBonusQuery->where('branch_id', $branchId);
        }
        
        $shareBonusEntries = $shareBonusQuery->get();
        
        // Calculate total share bonus from stored entries
        $totalShareBonus = $shareBonusEntries->sum('amount');
        
        // Get all active savings accounts for display purposes
        $savingsQuery = SavingsAccount::where('status', 'active');
        
        if ($branchId) {
            $savingsQuery->where('branch_id', $branchId);
        }
        
        $savingsAccounts = $savingsQuery->with('member')->get();
        
        // Create bonus details from stored entries
        $bonusDetails = [];
        foreach ($shareBonusEntries as $entry) {
            $bonusDetails[] = [
                'title' => $entry->title,
                'amount' => $entry->amount,
                'date' => $entry->date,
                'description' => $entry->description,
                'branch' => $entry->branch ? $entry->branch->name : 'All Branches',
            ];
        }
        
        return [
            'total_share_bonus' => $totalShareBonus,
            'members_with_savings' => $savingsAccounts,
            'bonus_details' => $bonusDetails,
        ];
    }
}