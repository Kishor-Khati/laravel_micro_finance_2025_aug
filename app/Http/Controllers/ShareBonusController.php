<?php

namespace App\Http\Controllers;

use App\Models\ShareBonusStatement;
use App\Models\ShareBonusRecord;
use App\Models\SavingsAccount;
use App\Models\Transaction;
use App\Models\Member;
use App\Models\Branch;
use App\Models\Expense;
use App\Exports\ShareBonusExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class ShareBonusController extends Controller
{
    public function index(Request $request)
    {
        $query = ShareBonusStatement::with(['branch', 'generatedBy']);

        // Apply date range filtering
        if ($request->filled('start_date')) {
            $query->where('period_start_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('period_end_date', '<=', $request->end_date);
        }

        // Apply branch filtering
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Apply status filtering
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $statements = $query->latest('generated_date')->paginate(15);
        $branches = Branch::active()->get();

        // Get summary statistics
        $totalStatements = ShareBonusStatement::count();
        $totalBonusDistributed = ShareBonusStatement::sum('total_share_bonus_pool');
        $recentStatements = ShareBonusStatement::with(['branch', 'generatedBy'])
            ->latest('generated_date')
            ->limit(5)
            ->get();

        return view('admin.share-bonus.index', compact(
            'statements', 
            'branches', 
            'totalStatements', 
            'totalBonusDistributed',
            'recentStatements'
        ));
    }

    public function generate(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'branch_id' => 'nullable|exists:branches,id',
                'share_bonus_percentage' => 'required|numeric|min:0|max:100',
            ]);

            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $branchId = $request->branch_id;
            $shareBonusPercentage = $request->share_bonus_percentage;

            // Check if statement already exists for overlapping period and branch
            $existingStatement = ShareBonusStatement::where(function ($query) use ($startDate, $endDate) {
                    $query->where(function ($q) use ($startDate, $endDate) {
                        // Check for overlapping periods
                        $q->where('period_start_date', '<=', $endDate->toDateString())
                          ->where('period_end_date', '>=', $startDate->toDateString());
                    });
                })
                ->where(function ($query) use ($branchId) {
                    if ($branchId) {
                        $query->where('branch_id', $branchId);
                    } else {
                        $query->whereNull('branch_id');
                    }
                })
                ->first();

            if ($existingStatement) {
                return back()->with('error', 'Share bonus statement already exists for an overlapping period (' . 
                    $existingStatement->period_start_date . ' to ' . $existingStatement->period_end_date . ') and branch.');
            }

            // Get financial data
            $financialData = $this->getFinancialData($startDate, $endDate, $branchId);
            
            if ($financialData['net_income'] <= 0) {
                return back()->with('error', 'No positive net income found for the selected period. Cannot generate share bonus.');
            }

            // Calculate share bonus distribution
            $shareBonusData = $this->calculateShareBonus(
                $financialData['net_income'],
                $shareBonusPercentage,
                $branchId,
                $startDate,
                $endDate
            );

            if (empty($shareBonusData['members'])) {
                return back()->with('error', 'No eligible members found for share bonus distribution.');
            }

            // Create statement record
            $statement = ShareBonusStatement::create([
                'branch_id' => $branchId,
                'period_start_date' => $startDate->toDateString(),
                'period_end_date' => $endDate->toDateString(),
                'total_raw_income' => $financialData['total_raw_income'],
                'total_expenses' => $financialData['total_expenses'],
                'net_income' => $financialData['net_income'],
                'share_bonus_percentage' => $shareBonusPercentage,
                'total_share_bonus_pool' => $shareBonusData['total_share_bonus'],
                'total_distributed_amount' => 0, // Will be updated when applied
                'total_eligible_members' => count($shareBonusData['members']),
                'total_members_received' => 0,
                'total_savings_balance' => $shareBonusData['total_savings_balance'],
                'financial_summary' => $financialData,
                'status' => 'generated',
            ]);

            // Create share bonus records for each member
            foreach ($shareBonusData['members'] as $memberData) {
                ShareBonusRecord::create([
                    'member_id' => $memberData['member_id'],
                    'savings_account_id' => $memberData['savings_account_id'],
                    'branch_id' => $branchId,
                    'bonus_amount' => $memberData['bonus_amount'],
                    'savings_balance_at_calculation' => $memberData['savings_balance'],
                    'proportion_percentage' => $memberData['proportion_percentage'],
                    'calculation_date' => now()->toDateString(),
                    'period_start_date' => $startDate->toDateString(),
                    'period_end_date' => $endDate->toDateString(),
                    'total_net_income' => $financialData['net_income'],
                    'share_bonus_percentage' => $shareBonusPercentage,
                    'total_share_bonus_pool' => $shareBonusData['total_share_bonus'],
                    'status' => 'calculated',
                ]);
            }

            // Handle export requests
            if ($request->has('export_pdf')) {
                return $this->exportPDF($statement->id);
            }

            if ($request->has('export_excel')) {
                return $this->exportExcel($statement->id);
            }

            return back()->with('success', 'Share bonus statement generated successfully! Statement Number: ' . $statement->statement_number);

        } catch (\Exception $e) {
            Log::error('Share bonus generation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate share bonus statement. Please try again.');
        }
    }

    public function apply(Request $request)
    {
        $request->validate([
            'statement_id' => 'required|exists:share_bonus_statements,id',
            'selected_members' => 'required|array|min:1',
            'selected_members.*' => 'exists:share_bonus_records,id',
        ]);

        try {
            DB::beginTransaction();

            $statement = ShareBonusStatement::findOrFail($request->statement_id);
            
            if (!$statement->canBeApplied()) {
                return back()->with('error', 'This statement cannot be applied in its current status.');
            }

            $selectedRecords = ShareBonusRecord::whereIn('id', $request->selected_members)
                ->where('status', 'calculated')
                ->get();

            if ($selectedRecords->isEmpty()) {
                return back()->with('error', 'No valid records found for application.');
            }

            $appliedCount = 0;
            $totalAppliedAmount = 0;

            foreach ($selectedRecords as $record) {
                // Update savings account balance
                $savingsAccount = SavingsAccount::findOrFail($record->savings_account_id);
                $savingsAccount->balance += $record->bonus_amount;
                $savingsAccount->save();

                // Create transaction record
                Transaction::create([
                    'member_id' => $record->member_id,
                    'savings_account_id' => $record->savings_account_id,
                    'branch_id' => $record->branch_id,
                    'transaction_type' => 'deposit',
                    'amount' => $record->bonus_amount,
                    'description' => 'Share Bonus - Period: ' . $record->period_start_date . ' to ' . $record->period_end_date,
                    'reference_number' => 'SB-' . $record->record_number,
                    'processed_by' => Auth::id(),
                ]);

                // Mark record as applied
                $record->markAsApplied();
                
                $appliedCount++;
                $totalAppliedAmount += $record->bonus_amount;
            }

            // Update statement status
            $statement->updateDistributionStatus();

            DB::commit();

            return back()->with('success', "Share bonus applied successfully to {$appliedCount} members. Total amount: Rs. " . number_format($totalAppliedAmount, 2));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Share bonus application failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to apply share bonus. Please try again.');
        }
    }

    public function undo(Request $request)
    {
        $request->validate([
            'statement_id' => 'required|exists:share_bonus_statements,id',
        ]);

        try {
            DB::beginTransaction();

            $statement = ShareBonusStatement::findOrFail($request->statement_id);
            
            $appliedRecords = ShareBonusRecord::where('period_start_date', $statement->period_start_date)
                ->where('period_end_date', $statement->period_end_date)
                ->when($statement->branch_id, function ($query) use ($statement) {
                    return $query->where('branch_id', $statement->branch_id);
                })
                ->where('status', 'applied')
                ->get();

            if ($appliedRecords->isEmpty()) {
                return back()->with('error', 'No applied share bonus records found to undo.');
            }

            $undoCount = 0;
            $totalUndoAmount = 0;

            foreach ($appliedRecords as $record) {
                // Update savings account balance
                $savingsAccount = SavingsAccount::findOrFail($record->savings_account_id);
                $savingsAccount->balance -= $record->bonus_amount;
                $savingsAccount->save();

                // Create reversal transaction
                Transaction::create([
                    'member_id' => $record->member_id,
                    'savings_account_id' => $record->savings_account_id,
                    'branch_id' => $record->branch_id,
                    'transaction_type' => 'withdrawal',
                    'amount' => $record->bonus_amount,
                    'description' => 'Share Bonus Reversal - Period: ' . $record->period_start_date . ' to ' . $record->period_end_date,
                    'reference_number' => 'SBR-' . $record->record_number,
                    'processed_by' => Auth::id(),
                ]);

                // Mark record as reversed
                $record->markAsReversed();
                
                $undoCount++;
                $totalUndoAmount += $record->bonus_amount;
            }

            // Update statement status
            $statement->updateDistributionStatus();

            DB::commit();

            return back()->with('success', "Share bonus reversed successfully for {$undoCount} members. Total amount: Rs. " . number_format($totalUndoAmount, 2));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Share bonus undo failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to undo share bonus. Please try again.');
        }
    }

    private function getFinancialData($startDate, $endDate, $branchId = null)
    {
        // Get loan interest income from loan payments (raw income)
        $loanInterestQuery = Transaction::where('transaction_type', 'loan_payment')
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        if ($branchId) {
            $loanInterestQuery->where('branch_id', $branchId);
        }
        
        $totalRawIncome = $loanInterestQuery->sum('interest_amount');

        // Get total expenses
        $expenseQuery = Expense::whereBetween('expense_date', [$startDate, $endDate]);
        
        if ($branchId) {
            $expenseQuery->where('branch_id', $branchId);
        }
        
        $totalExpenses = $expenseQuery->sum('amount');

        // Calculate net income
        $netIncome = $totalRawIncome - $totalExpenses;

        return [
            'total_raw_income' => $totalRawIncome,
            'total_expenses' => $totalExpenses,
            'net_income' => $netIncome,
            'period_start' => $startDate->toDateString(),
            'period_end' => $endDate->toDateString(),
            'branch_id' => $branchId,
        ];
    }

    private function calculateShareBonus($netIncome, $shareBonusPercentage, $branchId, $startDate, $endDate)
    {
        // Calculate total share bonus pool
        $totalShareBonus = ($netIncome * $shareBonusPercentage) / 100;

        // Get active savings accounts
        $savingsAccountsQuery = SavingsAccount::with('member')
            ->where('status', 'active')
            ->where('balance', '>', 0);
        
        if ($branchId) {
            $savingsAccountsQuery->where('branch_id', $branchId);
        }
        
        $savingsAccounts = $savingsAccountsQuery->get();

        if ($savingsAccounts->isEmpty()) {
            return [
                'total_share_bonus' => $totalShareBonus,
                'total_savings_balance' => 0,
                'members' => [],
            ];
        }

        // Calculate total savings balance
        $totalSavingsBalance = $savingsAccounts->sum('balance');

        // Calculate individual share bonuses
        $members = [];
        $totalDistributed = 0;

        foreach ($savingsAccounts as $account) {
            $proportionPercentage = ($account->balance / $totalSavingsBalance) * 100;
            $bonusAmount = ($account->balance / $totalSavingsBalance) * $totalShareBonus;
            
            $members[] = [
                'member_id' => $account->member_id,
                'savings_account_id' => $account->id,
                'member_name' => $account->member->name,
                'member_code' => $account->member->member_code,
                'savings_balance' => $account->balance,
                'proportion_percentage' => round($proportionPercentage, 4),
                'bonus_amount' => round($bonusAmount, 2),
            ];
            
            $totalDistributed += round($bonusAmount, 2);
        }

        return [
            'total_share_bonus' => $totalShareBonus,
            'total_distributed' => $totalDistributed,
            'total_savings_balance' => $totalSavingsBalance,
            'members' => $members,
        ];
    }

    public function exportPDF($statementId)
    {
        $statement = ShareBonusStatement::with(['branch', 'generatedBy'])->findOrFail($statementId);
        $records = ShareBonusRecord::where('period_start_date', $statement->period_start_date)
            ->where('period_end_date', $statement->period_end_date)
            ->when($statement->branch_id, function ($query) use ($statement) {
                return $query->where('branch_id', $statement->branch_id);
            })
            ->with('member')
            ->get();

        $pdf = Pdf::loadView('admin.share-bonus.pdf', compact('statement', 'records'));
        
        return $pdf->download('share-bonus-statement-' . $statement->statement_number . '.pdf');
    }

    public function exportExcel($statementId)
    {
        $statement = ShareBonusStatement::with(['branch', 'generatedBy'])->findOrFail($statementId);
        $records = ShareBonusRecord::where('period_start_date', $statement->period_start_date)
            ->where('period_end_date', $statement->period_end_date)
            ->when($statement->branch_id, function ($query) use ($statement) {
                return $query->where('branch_id', $statement->branch_id);
            })
            ->with('member')
            ->get();

        $data = [
            'statement' => $statement,
            'records' => $records,
            'financial_summary' => $statement->financial_summary,
        ];
        
        return Excel::download(
            new ShareBonusExport($data, $statement->period_start_date, $statement->period_end_date),
            'share-bonus-statement-' . $statement->statement_number . '.xlsx'
        );
    }

    public function show($id)
    {
        $statement = ShareBonusStatement::with(['branch', 'generatedBy'])->findOrFail($id);
        $records = ShareBonusRecord::where('period_start_date', $statement->period_start_date)
            ->where('period_end_date', $statement->period_end_date)
            ->when($statement->branch_id, function ($query) use ($statement) {
                return $query->where('branch_id', $statement->branch_id);
            })
            ->with('member')
            ->paginate(20);

        return view('admin.share-bonus.show', compact('statement', 'records'));
    }

    public function print($statementId)
    {
        $statement = ShareBonusStatement::with(['branch', 'generatedBy'])->findOrFail($statementId);
        $records = ShareBonusRecord::where('period_start_date', $statement->period_start_date)
            ->where('period_end_date', $statement->period_end_date)
            ->when($statement->branch_id, function ($query) use ($statement) {
                return $query->where('branch_id', $statement->branch_id);
            })
            ->with('member')
            ->get();

        return view('admin.share-bonus.print', compact('statement', 'records'));
    }

    public function destroy($id)
    {
        try {
            $statement = ShareBonusStatement::findOrFail($id);
            
            // Check if statement can be deleted (only if not applied)
            if ($statement->status !== 'generated') {
                return back()->with('error', 'Cannot delete statement that has been applied. Please undo the application first.');
            }

            // Delete related records first
            ShareBonusRecord::where('period_start_date', $statement->period_start_date)
                ->where('period_end_date', $statement->period_end_date)
                ->when($statement->branch_id, function ($query) use ($statement) {
                    return $query->where('branch_id', $statement->branch_id);
                })
                ->delete();

            // Delete the statement
            $statement->delete();

            return back()->with('success', 'Share bonus statement deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting statement: ' . $e->getMessage());
        }
    }
}