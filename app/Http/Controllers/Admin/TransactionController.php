<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\SavingsAccount;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['savingsAccount.member'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['savingsAccount.member']);
        return view('admin.transactions.show', compact('transaction'));
    }

    public function create()
    {
        $savingsAccounts = SavingsAccount::with('member')->get();
        return view('admin.transactions.create', compact('savingsAccounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'savings_account_id' => 'required|exists:savings_accounts,id',
            'type' => 'required|in:deposit,withdrawal',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $savingsAccount = SavingsAccount::findOrFail($request->savings_account_id);

        if ($request->type === 'withdrawal' && $request->amount > $savingsAccount->balance) {
            return redirect()->back()->withErrors(['amount' => 'Insufficient balance for withdrawal.']);
        }

        // Update savings account balance
        if ($request->type === 'deposit') {
            $savingsAccount->increment('balance', $request->amount);
        } else {
            $savingsAccount->decrement('balance', $request->amount);
        }

        // Create transaction record
        Transaction::create([
            'savings_account_id' => $request->savings_account_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'transaction_date' => now(),
        ]);

        return redirect()->route('admin.transactions')->with('success', 'Transaction processed successfully!');
    }
}