<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SavingsAccount;
use App\Models\Member;
use App\Models\SavingsType;
use App\Models\Transaction;
use Illuminate\Http\Request;

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
            'account_number' => 'required|string|unique:savings_accounts',
            'balance' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive,closed',
        ]);

        SavingsAccount::create($request->all());

        return redirect()->route('admin.savings')->with('success', 'Savings account created successfully!');
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

        return redirect()->route('admin.savings')->with('success', 'Savings account updated successfully!');
    }

    public function destroy(SavingsAccount $savingsAccount)
    {
        $savingsAccount->delete();
        return redirect()->route('admin.savings')->with('success', 'Savings account deleted successfully!');
    }

    public function deposit(Request $request, SavingsAccount $savingsAccount)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $savingsAccount->increment('balance', $request->amount);

        Transaction::create([
            'savings_account_id' => $savingsAccount->id,
            'type' => 'deposit',
            'amount' => $request->amount,
            'description' => $request->description ?? 'Deposit to savings account',
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

        $savingsAccount->decrement('balance', $request->amount);

        Transaction::create([
            'savings_account_id' => $savingsAccount->id,
            'type' => 'withdrawal',
            'amount' => $request->amount,
            'description' => $request->description ?? 'Withdrawal from savings account',
            'transaction_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Withdrawal processed successfully!');
    }
}