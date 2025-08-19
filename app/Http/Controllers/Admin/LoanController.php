<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Member;
use App\Models\LoanType;
use App\Models\LoanInstallment;
use App\Services\PenaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    public function index()
    {
        $loans = Loan::with(['member', 'loanType'])->paginate(15);
        return view('admin.loans.index', compact('loans'));
    }

    public function create()
    {
        $members = Member::all();
        $loanTypes = LoanType::all();
        $branches = \App\Models\Branch::all();
        return view('admin.loans.create', compact('members', 'loanTypes', 'branches'));
    }

    public function store(Request $request)
    {
        // Validate the request data directly (English dates)
        
        $request->validate([
            'loan_number' => 'nullable|string|unique:loans',
            'member_id' => 'required|exists:members,id',
            'loan_type_id' => 'required|exists:loan_types,id',
            'branch_id' => 'required|exists:branches,id',
            'requested_amount' => 'required|numeric|min:0',
            'approved_amount' => 'nullable|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'duration_months' => 'required|integer|min:1',
            'monthly_installment' => 'nullable|numeric|min:0',
            'purpose' => 'required|string',
            'collateral' => 'nullable|string',
            'status' => 'required|in:pending,approved,disbursed,closed,rejected',
            'application_date' => 'required|date',
            'approved_date' => 'nullable|date',
            'disbursed_date' => 'nullable|date',
            'maturity_date' => 'nullable|date',
            'approved_by' => 'nullable|exists:users,id',
            'remarks' => 'nullable|string',
        ]);
        
        // Generate loan number if not provided
        if (empty($data['loan_number'])) {
            $data['loan_number'] = $this->generateLoanNumber();
        }
        
        if (Auth::check()) {
            $data['approved_by'] = Auth::user()->id;
        } else {
            $data['approved_by'] = null;
        }
        
        $loan = Loan::create($data);

        // Create installments if loan is approved
        if ($request->status === 'approved' || $request->status === 'active') {
            $this->createInstallments($loan);
        }

        return redirect()->route('admin.loans.index')->with('success', 'Loan created successfully!');
    }

    public function show(Loan $loan)
    {
        $loan->load(['member', 'loanType', 'installments']);
        return view('admin.loans.show', compact('loan'));
    }

    public function edit(Loan $loan)
    {
        // Load loan data for editing
        
        $members = Member::all();
        $loanTypes = LoanType::all();
        $branches = \App\Models\Branch::all();
        return view('admin.loans.edit', compact('loan', 'members', 'loanTypes', 'branches'));
    }

    public function update(Request $request, Loan $loan)
    {
        // Validate the request data directly (English dates)
        
        $request->validate([
            'loan_number' => 'required|string|unique:loans,loan_number,' . $loan->id,
            'member_id' => 'required|exists:members,id',
            'loan_type_id' => 'required|exists:loan_types,id',
            'branch_id' => 'required|exists:branches,id',
            'requested_amount' => 'required|numeric|min:0',
            'approved_amount' => 'nullable|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'duration_months' => 'required|integer|min:1',
            'monthly_installment' => 'nullable|numeric|min:0',
            'purpose' => 'required|string',
            'collateral' => 'nullable|string',
            'status' => 'required|in:pending,approved,disbursed,closed,rejected',
            'application_date' => 'required|date',
            'approved_date' => 'nullable|date',
            'disbursed_date' => 'nullable|date',
            'maturity_date' => 'nullable|date',
            'approved_by' => 'nullable|exists:users,id',
            'remarks' => 'nullable|string',
        ]);
        
        // Only update approved_by if status is changing to approved
        if ($request->status === 'approved' && $loan->status !== 'approved') {
            if (Auth::check()) {
                $data['approved_by'] = Auth::user()->id;
            } else {
                $data['approved_by'] = null;
            }
        }
        
        $loan->update($data);

        return redirect()->route('admin.loans.index')->with('success', 'Loan updated successfully!');
    }

    public function destroy(Loan $loan)
    {
        $loan->delete();
        return redirect()->route('admin.loans.index')->with('success', 'Loan deleted successfully!');
    }

    private function generateLoanNumber()
    {
        $lastLoan = Loan::orderBy('id', 'desc')->first();
        $nextNumber = $lastLoan ? intval(substr($lastLoan->loan_number, -6)) + 1 : 1;
        return 'LN' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    private function createInstallments(Loan $loan)
    {
        $totalInterest = $loan->requested_amount * $loan->interest_rate / 100;
        $totalAmount = $loan->requested_amount + $totalInterest;
        $monthlyPayment = $totalAmount / $loan->duration_months;
        $monthlyPrincipal = $loan->requested_amount / $loan->duration_months;
        $monthlyInterest = $totalInterest / $loan->duration_months;
        
        for ($i = 1; $i <= $loan->duration_months; $i++) {
            LoanInstallment::create([
                'loan_id' => $loan->id,
                'installment_number' => $i,
                'principal_amount' => $monthlyPrincipal,
                'interest_amount' => $monthlyInterest,
                'total_amount' => $monthlyPayment,
                'outstanding_amount' => $monthlyPayment,
                'due_date' => now()->addMonths($i),
                'status' => 'pending',
            ]);
        }
    }

    /**
     * Calculate penalties for overdue installments
     */
    public function calculatePenalties(Request $request, PenaltyService $penaltyService)
    {
        $dailyRate = $request->input('daily_rate', PenaltyService::DEFAULT_DAILY_PENALTY_RATE);
        $updatedCount = $penaltyService->calculateAllPenalties($dailyRate);
        
        return redirect()->back()->with('success', "Penalties calculated for {$updatedCount} overdue installments.");
    }

    /**
     * Waive penalty for a specific installment
     */
    public function waivePenalty(Request $request, LoanInstallment $installment, PenaltyService $penaltyService)
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        $success = $penaltyService->waivePenalty($installment, $request->reason);
        
        if ($success) {
            return redirect()->back()->with('success', 'Penalty waived successfully.');
        }
        
        return redirect()->back()->with('error', 'Failed to waive penalty.');
    }

    /**
     * Waive all penalties for a loan
     */
    public function waiveLoanPenalties(Request $request, Loan $loan, PenaltyService $penaltyService)
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        $waivedCount = $penaltyService->waiveLoanPenalties($loan, $request->reason);
        
        return redirect()->back()->with('success', "Penalties waived for {$waivedCount} installments.");
    }

    /**
     * Show penalty statistics
     */
    public function penaltyStatistics(PenaltyService $penaltyService)
    {
        $statistics = $penaltyService->getPenaltyStatistics();
        return view('admin.loans.penalty-statistics', compact('statistics'));
    }
}