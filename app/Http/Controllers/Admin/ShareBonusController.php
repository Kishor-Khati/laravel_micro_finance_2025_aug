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
use App\Models\ShareBonusStatement;
use App\Models\ShareBonusRecord;
use App\Exports\ShareBonusExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
                'share_bonus_percentage' => 'required|numeric|min:0|max:100',
            ]);
            
            $startDate = Carbon::parse(Transaction::min('created_at') ?? now()->subYears(5))->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            $request->validate([
                'start_date' => 'required|string',
                'end_date' => 'required|string',
                'branch_id' => 'nullable|exists:branches,id',
                'share_bonus_percentage' => 'required|numeric|min:0|max:100',
            ]);
            
            $startDateAD = $request->start_date_ad ?? $request->start_date;
            $endDateAD = $request->end_date_ad ?? $request->end_date;
            
            $startDate = Carbon::parse($startDateAD)->startOfDay();
            $endDate = Carbon::parse($endDateAD)->endOfDay();
        }
        
        $branchId = $request->branch_id;
        $shareBonusPercentage = $request->share_bonus_percentage / 100;
        
        // Get all financial data for the period
        $data = $this->getFinancialData($startDate, $endDate, $branchId);
        
        // Calculate net income (raw income - expenses)
        $data['net_income'] = $data['total_raw_income'] - $data['total_expenses'];
        
        // Validate that net income is positive
        if ($data['net_income'] <= 0) {
            return redirect()->back()
                ->with('error', 'Cannot generate share bonus statement. Net income must be positive. Current net income: Rs. ' . number_format($data['net_income'], 2))
                ->withInput();
        }
        
        // Calculate total share bonus pool from net income
        $totalShareBonusPool = $data['net_income'] * $shareBonusPercentage;
        
        // Create or update share bonus statement
        $statement = $this->createShareBonusStatement($startDate, $endDate, $branchId, $data, $shareBonusPercentage, $totalShareBonusPool);
        
        // Calculate individual member bonuses
        $shareBonus = $this->calculateShareBonus($totalShareBonusPool, $startDate, $endDate, $branchId, $statement->id);
        
        // Add share bonus data to the financial data
        $data['share_bonus'] = $shareBonus;
        $data['share_bonus_statement'] = $statement;
        $data['final_balance'] = $data['net_income'] - $totalShareBonusPool;
        
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
     * Create a share bonus statement record
     */
    private function createShareBonusStatement($startDate, $endDate, $branchId, $data, $shareBonusPercentage, $totalShareBonusPool)
    {
        // Check if statement already exists for this period and branch
        $existingStatement = ShareBonusStatement::where('period_start_date', $startDate->toDateString())
            ->where('period_end_date', $endDate->toDateString())
            ->where('branch_id', $branchId)
            ->first();
            
        if ($existingStatement) {
            // Update existing statement
            $existingStatement->update([
                'total_raw_income' => $data['total_raw_income'],
                'total_expenses' => $data['total_expenses'],
                'net_income' => $data['net_income'],
                'share_bonus_percentage' => $shareBonusPercentage * 100,
                'total_share_bonus_pool' => $totalShareBonusPool,
                'status' => 'generated',
                'generated_by' => Auth::id(),
                'generated_date' => now()->toDateString(),
            ]);
            
            return $existingStatement;
        }
        
        // Create new statement
        return ShareBonusStatement::create([
            'branch_id' => $branchId,
            'period_start_date' => $startDate->toDateString(),
            'period_end_date' => $endDate->toDateString(),
            'total_raw_income' => $data['total_raw_income'],
            'total_expenses' => $data['total_expenses'],
            'net_income' => $data['net_income'],
            'share_bonus_percentage' => $shareBonusPercentage * 100,
            'total_share_bonus_pool' => $totalShareBonusPool,
            'status' => 'generated',
            'generated_by' => Auth::id(),
            'generated_date' => now()->toDateString(),
        ]);
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
                'share_bonus_percentage' => 'required|numeric|min:0|max:100',
            ]);
            
            // Set start date to the earliest record and end date to today
            $startDate = Carbon::parse(Transaction::min('created_at') ?? now()->subYears(5))->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            $request->validate([
                'start_date' => 'required|string',
                'end_date' => 'required|string',
                'branch_id' => 'nullable|exists:branches,id',
                'share_bonus_percentage' => 'required|numeric|min:0|max:100',
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
        $shareBonusPercentage = $request->share_bonus_percentage / 100; // Convert percentage to decimal
        $shareBonus = $this->calculateShareBonus($data['net_income'], $startDate, $endDate, $branchId, $shareBonusPercentage);
        
        // Add share bonus data to the financial data
        $data['share_bonus'] = $shareBonus;
        
        // Final balance remains the same as net income (share bonus is distributed from net income, not deducted)
        $data['final_balance'] = $data['net_income'];
        
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
                'share_bonus_percentage' => 'required|numeric|min:0|max:100',
            ]);
            
            // Set start date to the earliest record and end date to today
            $startDate = Carbon::parse(Transaction::min('created_at') ?? now()->subYears(5))->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            $request->validate([
                'start_date' => 'required|string',
                'end_date' => 'required|string',
                'branch_id' => 'nullable|exists:branches,id',
                'share_bonus_percentage' => 'required|numeric|min:0|max:100',
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
        $shareBonusPercentage = $request->share_bonus_percentage / 100; // Convert percentage to decimal
        $shareBonus = $this->calculateShareBonus($data['net_income'], $startDate, $endDate, $branchId, $shareBonusPercentage);
        
        // Add share bonus data to the financial data
        $data['share_bonus'] = $shareBonus;
        
        // Final balance remains the same as net income (share bonus is distributed from net income, not deducted)
        $data['final_balance'] = $data['net_income'];
        
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
        $totalRawIncome = $query->where('transaction_type', 'loan_payment')
            ->sum('interest_amount');
        
        // Get total expenses
        $totalExpenses = $expenseQuery->sum('amount');
        
        // Get loan data
        $totalInterest = $query->where('transaction_type', 'loan_payment')
            ->sum('interest_amount');
            
        $loans = [
            'total_loans' => $loanQuery->count(),
            'total_amount' => $loanQuery->sum('approved_amount'),
            'total_interest' => $totalInterest,
            'active_loans' => $loanQuery->where('status', 'active')->count(),
            'active_amount' => $loanQuery->where('status', 'active')->sum('approved_amount'),
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
            'deposits' => $query->where('transaction_type', 'deposit')->sum('amount'),
            'withdrawals' => $query->where('transaction_type', 'withdrawal')->sum('amount'),
            'loan_disbursements' => $query->where('transaction_type', 'loan_disbursement')->sum('amount'),
            'loan_payments' => $query->where('transaction_type', 'loan_payment')->sum('amount'),
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
     * Apply share bonuses to selected members' savings accounts
     */
    public function apply(Request $request)
    {
        $request->validate([
            'selected_members' => 'required|array|min:1',
            'selected_members.*' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'branch_id' => 'nullable|exists:branches,id',
            'share_bonus_percentage' => 'required|numeric|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $branchId = $request->branch_id;
            $selectedAccountNumbers = $request->selected_members;

            // Get financial data and calculate share bonuses
            $data = $this->getFinancialData($startDate, $endDate, $branchId);
            $shareBonusPercentage = $request->share_bonus_percentage / 100; // Convert percentage to decimal
            $shareBonus = $this->calculateShareBonus($data['total_raw_income'], $startDate, $endDate, $branchId, $shareBonusPercentage);

            $appliedBonuses = [];
            $totalApplied = 0;

            // Apply bonuses to selected members
            foreach ($shareBonus['member_bonuses'] as $bonus) {
                if (in_array($bonus['account_number'], $selectedAccountNumbers)) {
                    // Find the savings account
                    $savingsAccount = SavingsAccount::where('account_number', $bonus['account_number'])->first();
                    
                    if ($savingsAccount) {
                        // Add bonus to savings account balance
                        $savingsAccount->balance += $bonus['bonus_amount'];
                        $savingsAccount->save();

                        // Create a transaction record for the bonus
                        Transaction::create([
                            'member_id' => $savingsAccount->member_id,
                            'savings_account_id' => $savingsAccount->id,
                            'transaction_type' => 'share_bonus',
                            'amount' => $bonus['bonus_amount'],
                            'balance_after' => $savingsAccount->balance,
                            'description' => 'Share bonus applied for period ' . $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d'),
                            'status' => 'completed',
                            'processed_by' => Auth::id(),
                        ]);

                        $appliedBonuses[] = [
                            'account_number' => $bonus['account_number'],
                            'member_name' => $bonus['member_name'],
                            'bonus_amount' => $bonus['bonus_amount'],
                            'new_balance' => $savingsAccount->balance,
                        ];

                        $totalApplied += $bonus['bonus_amount'];
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.share-bonus.index')
                ->with('success', 'Share bonuses successfully applied to ' . count($appliedBonuses) . ' members. Total amount: Rs. ' . number_format($totalApplied, 2))
                ->with('applied_bonuses', $appliedBonuses);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to apply share bonuses: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Undo share bonus transactions for a given period
     */
    public function undo(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'branch_id' => 'nullable|exists:branches,id',
            'share_bonus_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $branchId = $request->branch_id;
        $shareBonusPercentage = $request->share_bonus_percentage / 100;

        try {
            DB::beginTransaction();

            // Find all share bonus transactions in the specified period
            $transactionsQuery = Transaction::where('transaction_type', 'share_bonus')
                ->whereBetween('created_at', [$startDate, $endDate]);

            if ($branchId) {
                $transactionsQuery->whereHas('member', function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                });
            }

            $transactions = $transactionsQuery->get();

            if ($transactions->isEmpty()) {
                return redirect()->back()
                    ->with('error', 'No share bonus transactions found for the specified period.');
            }

            $undoCount = 0;
            $totalUndone = 0;

            foreach ($transactions as $transaction) {
                $savingsAccount = $transaction->savingsAccount;
                
                // Reverse the transaction by subtracting the amount
                $savingsAccount->balance -= $transaction->amount;
                $savingsAccount->save();

                // Create a reversal transaction
                Transaction::create([
                    'member_id' => $savingsAccount->member_id,
                    'savings_account_id' => $savingsAccount->id,
                    'transaction_type' => 'share_bonus_reversal',
                    'amount' => -$transaction->amount,
                    'balance_after' => $savingsAccount->balance,
                    'description' => "Share bonus reversal for period {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}",
                    'status' => 'completed',
                    'processed_by' => Auth::id(),
                ]);

                // Mark the original transaction as reversed
                $transaction->update([
                    'description' => $transaction->description . ' (REVERSED)',
                ]);

                $undoCount++;
                $totalUndone += $transaction->amount;
            }

            DB::commit();

            return redirect()->route('admin.share-bonus.index')
                ->with('success', "Share bonuses reversed successfully for {$undoCount} transactions. Total amount reversed: Rs. " . number_format($totalUndone, 2));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'An error occurred while reversing share bonuses: ' . $e->getMessage());
        }
    }

    /**
     * Calculate share bonuses based on savings balances
     */
    private function calculateShareBonus($totalShareBonusPool, $startDate, $endDate, $branchId = null, $statementId = null)
    {
        // Get all active savings accounts
        $savingsQuery = SavingsAccount::where('status', 'active');
        
        if ($branchId) {
            $savingsQuery->whereHas('member', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }
        
        $savingsAccounts = $savingsQuery->with(['member', 'branch'])->get();
        
        // Calculate total savings balance for proportion calculations
        $totalSavingsBalance = $savingsAccounts->sum('balance');
        
        if ($totalSavingsBalance <= 0) {
            return [
                'total_share_bonus_pool' => $totalShareBonusPool,
                'total_distributed' => 0,
                'member_bonuses' => [],
                'members_with_savings' => $savingsAccounts,
                'bonus_details' => [],
                'error' => 'No savings balance found for distribution'
            ];
        }
        
        // Create member bonuses based on savings proportions
        $memberBonuses = [];
        $totalDistributed = 0;
        
        // Clear existing records for this period if regenerating
        if ($statementId) {
            ShareBonusRecord::where('period_start_date', $startDate->toDateString())
                ->where('period_end_date', $endDate->toDateString())
                ->where('branch_id', $branchId)
                ->delete();
        }
        
        foreach ($savingsAccounts as $account) {
            if ($account->balance > 0) {
                $proportion = $account->balance / $totalSavingsBalance;
                $bonusAmount = $totalShareBonusPool * $proportion;
                $proportionPercentage = $proportion * 100;
                
                // Create share bonus record
                if ($statementId) {
                    ShareBonusRecord::create([
                        'member_id' => $account->member_id,
                        'savings_account_id' => $account->id,
                        'branch_id' => $account->member->branch_id,
                        'bonus_amount' => $bonusAmount,
                        'savings_balance_at_calculation' => $account->balance,
                        'proportion_percentage' => $proportionPercentage,
                        'calculation_date' => now()->toDateString(),
                        'period_start_date' => $startDate->toDateString(),
                        'period_end_date' => $endDate->toDateString(),
                        'total_net_income' => $totalShareBonusPool / ($proportion > 0 ? ($totalShareBonusPool / $totalShareBonusPool) : 1),
                        'share_bonus_percentage' => ($totalShareBonusPool / ($totalSavingsBalance > 0 ? $totalSavingsBalance : 1)) * 100,
                        'total_share_bonus_pool' => $totalShareBonusPool,
                        'status' => 'calculated',
                        'calculated_by' => Auth::id(),
                    ]);
                }
                
                $memberBonuses[] = [
                    'account_number' => $account->account_number,
                    'member_name' => $account->member->first_name . ' ' . $account->member->last_name,
                    'member_id' => $account->member->id,
                    'savings_account_id' => $account->id,
                    'savings_balance' => $account->balance,
                    'proportion' => $proportion,
                    'proportion_percentage' => $proportionPercentage,
                    'bonus_amount' => $bonusAmount,
                ];
                
                $totalDistributed += $bonusAmount;
            }
        }
        
        // Update statement with calculated totals
        if ($statementId) {
            $statement = ShareBonusStatement::find($statementId);
            if ($statement) {
                $statement->update([
                    'total_eligible_members' => count($memberBonuses),
                    'total_savings_balance' => $totalSavingsBalance,
                ]);
            }
        }
        
        return [
            'total_share_bonus_pool' => $totalShareBonusPool,
            'total_distributed' => $totalDistributed,
            'member_bonuses' => $memberBonuses,
            'members_with_savings' => $savingsAccounts,
            'bonus_details' => $memberBonuses,
            'total_savings_balance' => $totalSavingsBalance,
        ];
    }
}