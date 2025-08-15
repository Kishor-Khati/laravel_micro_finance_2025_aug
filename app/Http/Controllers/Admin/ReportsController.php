<?php

namespace App\Http\Controllers\Admin;

use App\Exports\BranchesExport;
use App\Exports\LoansExport;
use App\Exports\MembersExport;
use App\Exports\SavingsExport;
use App\Exports\SummaryExport;
use App\Exports\TransactionsExport;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Expense;
use App\Models\Loan;
use App\Models\Member;
use App\Models\SavingsAccount;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportsController extends Controller
{
    /**
     * Display the reports dashboard.
     */
    public function index()
    {
        $totalMembers = Member::count();
        $totalLoans = Loan::count();
        $totalSavings = SavingsAccount::count();
        $totalTransactions = Transaction::count();
        
        $activeLoans = Loan::where('status', 'active')->count();
        $completedLoans = Loan::where('status', 'completed')->count();
        
        $activeSavings = SavingsAccount::where('status', 'active')->count();
        
        return view('admin.reports.index', compact(
            'totalMembers', 
            'totalLoans', 
            'totalSavings', 
            'totalTransactions',
            'activeLoans',
            'completedLoans',
            'activeSavings'
        ));
    }
    
    /**
     * Export members to Excel.
     */
    public function exportMembersExcel()
    {
        return Excel::download(new MembersExport, 'members.xlsx');
    }
    
    /**
     * Export members to PDF.
     */
    public function exportMembersPdf()
    {
        $members = Member::all();
        $pdf = PDF::loadView('admin.reports.pdf.members', compact('members'));
        return $pdf->download('members.pdf');
    }
    
    /**
     * Export loans to Excel.
     */
    public function exportLoansExcel()
    {
        return Excel::download(new LoansExport, 'loans.xlsx');
    }
    
    /**
     * Export loans to PDF.
     */
    public function exportLoansPdf()
    {
        $loans = Loan::with(['member', 'loanType'])->get();
        $pdf = PDF::loadView('admin.reports.pdf.loans', compact('loans'));
        return $pdf->download('loans.pdf');
    }
    
    /**
     * Export savings to Excel.
     */
    public function exportSavingsExcel()
    {
        return Excel::download(new SavingsExport, 'savings.xlsx');
    }
    
    /**
     * Export savings to PDF.
     */
    public function exportSavingsPdf()
    {
        $savingsAccounts = SavingsAccount::with(['member', 'savingsType'])->get();
        $pdf = PDF::loadView('admin.reports.pdf.savings', compact('savingsAccounts'));
        return $pdf->download('savings.pdf');
    }
    
    /**
     * Export transactions to Excel.
     */
    public function exportTransactionsExcel()
    {
        return Excel::download(new TransactionsExport, 'transactions.xlsx');
    }
    
    /**
     * Export transactions to PDF.
     */
    public function exportTransactionsPdf()
    {
        $transactions = Transaction::with(['member', 'savingsAccount', 'loan'])->get();
        $pdf = PDF::loadView('admin.reports.pdf.transactions', compact('transactions'));
        return $pdf->download('transactions.pdf');
    }
    
    /**
     * Export branches to Excel.
     */
    public function exportBranchesExcel()
    {
        return Excel::download(new BranchesExport, 'branches.xlsx');
    }
    
    /**
     * Export branches to PDF.
     */
    public function exportBranchesPdf()
    {
        $branches = Branch::with(['manager', 'members', 'loans', 'savingsAccounts'])->get();
        $pdf = PDF::loadView('admin.reports.pdf.branches', compact('branches'));
        return $pdf->download('branches.pdf');
    }
    
    /**
     * Export executive summary to Excel.
     */
    public function exportSummaryExcel()
    {
        return Excel::download(new SummaryExport, 'executive_summary.xlsx');
    }
    
    /**
     * Export executive summary to PDF.
     */
    public function exportSummaryPdf()
    {
        $data = [
            'members' => [
                'count' => Member::count(),
                'active_count' => Member::whereHas('savingsAccounts', function($q) {
                    $q->where('status', 'active');
                })->count(),
            ],
            'loans' => [
                'count' => Loan::count(),
                'active_count' => Loan::where('status', 'active')->count(),
                'active_amount' => Loan::where('status', 'active')->sum('amount'),
                'pending_count' => Loan::where('status', 'pending')->count(),
                'pending_amount' => Loan::where('status', 'pending')->sum('amount'),
                'completed_count' => Loan::where('status', 'completed')->count(),
                'completed_amount' => Loan::where('status', 'completed')->sum('amount'),
                'amount' => Loan::sum('amount'),
            ],
            'savings' => [
                'count' => SavingsAccount::count(),
                'amount' => SavingsAccount::sum('balance'),
            ],
            'transactions' => [
                'count' => Transaction::count(),
                'amount' => Transaction::sum('amount'),
            ],
            'expenses' => [
                'count' => Expense::count(),
                'amount' => Expense::sum('amount'),
            ],
        ];
        
        $pdf = PDF::loadView('admin.reports.pdf.summary', compact('data'));
        return $pdf->download('executive_summary.pdf');
    }
}