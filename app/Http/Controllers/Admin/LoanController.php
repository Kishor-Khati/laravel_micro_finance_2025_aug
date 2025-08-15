<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Member;
use App\Models\LoanType;
use App\Models\LoanInstallment;
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
        return view('admin.loans.create', compact('members', 'loanTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'loan_type_id' => 'required|exists:loan_types,id',
            'amount' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'term_months' => 'required|integer|min:1',
            'purpose' => 'nullable|string',
            'status' => 'required|in:pending,approved,active,closed,defaulted',
        ]);

        $data = $request->all();
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

        return redirect()->route('admin.loans')->with('success', 'Loan created successfully!');
    }

    public function show(Loan $loan)
    {
        $loan->load(['member', 'loanType', 'installments']);
        return view('admin.loans.show', compact('loan'));
    }

    public function edit(Loan $loan)
    {
        $members = Member::all();
        $loanTypes = LoanType::all();
        return view('admin.loans.edit', compact('loan', 'members', 'loanTypes'));
    }

    public function update(Request $request, Loan $loan)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'loan_type_id' => 'required|exists:loan_types,id',
            'amount' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'term_months' => 'required|integer|min:1',
            'purpose' => 'nullable|string',
            'status' => 'required|in:pending,approved,active,closed,defaulted',
        ]);

        $data = $request->all();
        
        // Only update approved_by if status is changing to approved
        if ($request->status === 'approved' && $loan->status !== 'approved') {
            if (Auth::check()) {
                $data['approved_by'] = Auth::user()->id;
            } else {
                $data['approved_by'] = null;
            }
        }
        
        $loan->update($data);

        return redirect()->route('admin.loans')->with('success', 'Loan updated successfully!');
    }

    public function destroy(Loan $loan)
    {
        $loan->delete();
        return redirect()->route('admin.loans')->with('success', 'Loan deleted successfully!');
    }

    private function createInstallments(Loan $loan)
    {
        $monthlyPayment = ($loan->amount + ($loan->amount * $loan->interest_rate / 100)) / $loan->term_months;
        
        for ($i = 1; $i <= $loan->term_months; $i++) {
            LoanInstallment::create([
                'loan_id' => $loan->id,
                'installment_number' => $i,
                'due_date' => now()->addMonths($i),
                'amount' => $monthlyPayment,
                'status' => 'pending',
            ]);
        }
    }
}