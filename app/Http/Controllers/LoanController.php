<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Member;
use App\Models\LoanType;
use App\Models\Branch;
use App\Models\LoanInstallment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-loans')->only(['index', 'show']);
        $this->middleware('permission:create-loans')->only(['create', 'store']);
        $this->middleware('permission:approve-loans')->only(['approve']);
        $this->middleware('permission:manage-loans')->only(['disburse', 'reject', 'close']);
    }
    
    public function index(Request $request)
    {
        $query = Loan::with(['member', 'loanType', 'branch'])
            ->orderBy('created_at', 'desc');
            
        // Apply branch filter for users without all-branches permission
        if (!Auth::user()->hasPermission('view-all-branches')) {
            $query->where('branch_id', Auth::user()->branch_id);
        }
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('loan_number', 'like', "%{$search}%")
                  ->orWhereHas('member', function($mq) use ($search) {
                      $mq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('member_number', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('loan_type_id')) {
            $query->where('loan_type_id', $request->loan_type_id);
        }
        
        $loans = $query->paginate(15);
        $loanTypes = LoanType::active()->get();
        $branches = Branch::active()->get();
        
        return view('loans.index', compact('loans', 'loanTypes', 'branches'));
    }
    
    public function create()
    {
        $members = Member::active()->kycVerified()->get();
        $loanTypes = LoanType::active()->get();
        $branches = Branch::active()->get();
        
        return view('loans.create', compact('members', 'loanTypes', 'branches'));
    }
    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'member_id' => 'required|exists:members,id',
            'loan_type_id' => 'required|exists:loan_types,id',
            'branch_id' => 'required|exists:branches,id',
            'requested_amount' => 'required|numeric|min:1000',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'duration_months' => 'required|integer|min:1|max:360',
            'purpose' => 'required|string',
            'collateral' => 'nullable|string',
        ]);
        
        $validatedData['loan_number'] = $this->generateLoanNumber();
        $validatedData['application_date'] = now();
        $validatedData['status'] = 'pending';
        
        $loan = Loan::create($validatedData);
        
        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan application submitted successfully.');
    }
    
    public function show(Loan $loan)
    {
        $loan->load(['member', 'loanType', 'branch', 'approvedBy', 'installments']);
        return view('loans.show', compact('loan'));
    }
    
    public function approve(Request $request, Loan $loan)
    {
        $request->validate([
            'approved_amount' => 'required|numeric|min:1000',
            'remarks' => 'nullable|string',
        ]);
        
        DB::transaction(function () use ($request, $loan) {
            $loan->update([
                'approved_amount' => $request->approved_amount,
                'status' => 'approved',
                'approved_date' => now(),
                'approved_by' => Auth::id(),
                'remarks' => $request->remarks,
                'monthly_installment' => $this->calculateMonthlyInstallment(
                    $request->approved_amount,
                    $loan->interest_rate,
                    $loan->duration_months
                ),
                'maturity_date' => now()->addMonths($loan->duration_months),
            ]);
            
            // Generate installment schedule
            $this->generateInstallmentSchedule($loan);
        });
        
        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan approved successfully.');
    }
    
    public function disburse(Request $request, Loan $loan)
    {
        if ($loan->status !== 'approved') {
            return back()->with('error', 'Only approved loans can be disbursed.');
        }
        
        DB::transaction(function () use ($loan) {
            $loan->update([
                'status' => 'disbursed',
                'disbursed_date' => now(),
            ]);
            
            // Create disbursement transaction
            Transaction::create([
                'transaction_number' => $this->generateTransactionNumber(),
                'member_id' => $loan->member_id,
                'branch_id' => $loan->branch_id,
                'transaction_type' => 'loan_disbursement',
                'amount' => $loan->approved_amount,
                'balance_before' => 0,
                'balance_after' => $loan->approved_amount,
                'reference_type' => 'loan',
                'reference_id' => $loan->id,
                'description' => "Loan disbursement for loan #{$loan->loan_number}",
                'processed_by' => Auth::id(),
                'transaction_date' => now(),
            ]);
        });
        
        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan disbursed successfully.');
    }
    
    public function reject(Request $request, Loan $loan)
    {
        $request->validate([
            'remarks' => 'required|string',
        ]);
        
        $loan->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'remarks' => $request->remarks,
        ]);
        
        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan rejected.');
    }
    
    private function generateLoanNumber()
    {
        $lastLoan = Loan::orderBy('id', 'desc')->first();
        $nextNumber = $lastLoan ? intval(substr($lastLoan->loan_number, -6)) + 1 : 1;
        return 'LN' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
    
    private function generateTransactionNumber()
    {
        $lastTransaction = Transaction::orderBy('id', 'desc')->first();
        $nextNumber = $lastTransaction ? intval(substr($lastTransaction->transaction_number, -8)) + 1 : 1;
        return 'TXN' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);
    }
    
    private function calculateMonthlyInstallment($principal, $annualRate, $months)
    {
        $monthlyRate = $annualRate / 100 / 12;
        if ($monthlyRate == 0) {
            return $principal / $months;
        }
        
        return $principal * ($monthlyRate * pow(1 + $monthlyRate, $months)) / 
               (pow(1 + $monthlyRate, $months) - 1);
    }
    
    private function generateInstallmentSchedule(Loan $loan)
    {
        $principal = $loan->approved_amount;
        $monthlyRate = $loan->interest_rate / 100 / 12;
        $monthlyInstallment = $loan->monthly_installment;
        $balance = $principal;
        
        for ($i = 1; $i <= $loan->duration_months; $i++) {
            $interestAmount = $balance * $monthlyRate;
            $principalAmount = $monthlyInstallment - $interestAmount;
            $balance -= $principalAmount;
            
            LoanInstallment::create([
                'loan_id' => $loan->id,
                'installment_number' => $i,
                'principal_amount' => $principalAmount,
                'interest_amount' => $interestAmount,
                'total_amount' => $monthlyInstallment,
                'outstanding_amount' => $monthlyInstallment,
                'due_date' => $loan->disbursed_date ? 
                    Carbon::parse($loan->disbursed_date)->addMonths($i) : 
                    now()->addMonths($i),
            ]);
        }
    }
}