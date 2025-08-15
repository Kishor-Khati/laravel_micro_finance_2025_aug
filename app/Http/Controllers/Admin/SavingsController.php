<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SavingsAccount;
use App\Models\SavingsType;
use App\Models\Member;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavingsController extends Controller
{
    public function index()
    {
        $savingsAccounts = SavingsAccount::with(['member', 'savingsType'])->paginate(15);
        return view('admin.savings.index', compact('savingsAccounts'));
    }

    public function create()
    {
        $members = Member::all();
        $savingsTypes = SavingsType::all();
        return view('admin.savings.create', compact('members', 'savingsTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'savings_type_id' => 'required|exists:savings_types,id',
            'balance' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,closed',
        ]);
        
        $savingsType = SavingsType::findOrFail($request->savings_type_id);
        
        $data = $request->all();
        $data['account_number'] = $this->generateAccountNumber();
        $data['interest_rate'] = $savingsType->interest_rate;
        $data['opened_date'] = now();
        
        $savingsAccount = SavingsAccount::create($data);
        
        // Create initial deposit transaction if balance > 0
        if ($request->balance > 0) {
            Transaction::create([
                'transaction_number' => $this->generateTransactionNumber(),
                'member_id' => $request->member_id,
                'branch_id' => Auth::check() ? Auth::user()->branch_id : null,
                'transaction_type' => 'deposit',
                'amount' => $request->balance,
                'balance_before' => 0,
                'balance_after' => $request->balance,
                'reference_type' => 'savings_account',
                'reference_id' => $savingsAccount->id,
                'description' => "Initial deposit for account #{$savingsAccount->account_number}",
                'processed_by' => Auth::check() ? Auth::user()->id : null,
                'transaction_date' => now(),
            ]);
        }

        return redirect()->route('admin.savings.index')->with('success', 'Savings account created successfully!');
    }

    public function show(SavingsAccount $savingsAccount)
    {
        $savingsAccount->load(['member', 'savingsType', 'transactions']);
        return view('admin.savings.show', compact('savingsAccount'));
    }

    public function edit(SavingsAccount $savingsAccount)
    {
        $members = Member::all();
        $savingsTypes = SavingsType::all();
        return view('admin.savings.edit', compact('savingsAccount', 'members', 'savingsTypes'));
    }

    public function update(Request $request, SavingsAccount $savingsAccount)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'savings_type_id' => 'required|exists:savings_types,id',
            'account_number' => 'required|string|unique:savings_accounts,account_number,' . $savingsAccount->id,
            'balance' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive,closed',
        ]);

        $savingsAccount->update($request->all());

        return redirect()->route('admin.savings.index')->with('success', 'Savings account updated successfully!');
    }

    public function destroy(SavingsAccount $savingsAccount)
    {
        $savingsAccount->delete();
        return redirect()->route('admin.savings.index')->with('success', 'Savings account deleted successfully!');
    }

    public function deposit(Request $request, SavingsAccount $savingsAccount)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $previousBalance = $savingsAccount->balance;
        $newBalance = $previousBalance + $request->amount;
        
        $savingsAccount->update(['balance' => $newBalance]);

        Transaction::create([
            'transaction_number' => $this->generateTransactionNumber(),
            'member_id' => $savingsAccount->member_id,
            'branch_id' => Auth::check() ? Auth::user()->branch_id : null,
            'transaction_type' => 'deposit',
            'amount' => $request->amount,
            'balance_before' => $previousBalance,
            'balance_after' => $newBalance,
            'reference_type' => 'savings_account',
            'reference_id' => $savingsAccount->id,
            'description' => $request->description ?? "Deposit to account #{$savingsAccount->account_number}",
            'processed_by' => Auth::check() ? Auth::user()->id : null,
            'transaction_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Deposit processed successfully!');
    }

    public function withdraw(Request $request, SavingsAccount $savingsAccount)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $savingsAccount->balance,
            'description' => 'nullable|string|max:255',
        ]);

        $previousBalance = $savingsAccount->balance;
        $newBalance = $previousBalance - $request->amount;
        
        $savingsAccount->update(['balance' => $newBalance]);

        Transaction::create([
            'transaction_number' => $this->generateTransactionNumber(),
            'member_id' => $savingsAccount->member_id,
            'branch_id' => Auth::check() ? Auth::user()->branch_id : null,
            'transaction_type' => 'withdrawal',
            'amount' => $request->amount,
            'balance_before' => $previousBalance,
            'balance_after' => $newBalance,
            'reference_type' => 'savings_account',
            'reference_id' => $savingsAccount->id,
            'description' => $request->description ?? "Withdrawal from account #{$savingsAccount->account_number}",
            'processed_by' => Auth::check() ? Auth::user()->id : null,
            'transaction_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Withdrawal processed successfully!');
    }
    
    private function generateAccountNumber()
    {
        $lastAccount = SavingsAccount::orderBy('id', 'desc')->first();
        $nextNumber = $lastAccount ? intval(substr($lastAccount->account_number, -8)) + 1 : 1;
        return 'SA' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);
    }
    
    private function generateTransactionNumber()
    {
        $lastTransaction = Transaction::orderBy('id', 'desc')->first();
        $nextNumber = $lastTransaction ? intval(substr($lastTransaction->transaction_number, -8)) + 1 : 1;
        return 'TXN' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);
    }
}