<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\SavingsAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['reference', 'member', 'branch'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['reference', 'member', 'branch', 'processedBy']);
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
            'transaction_type' => 'required|in:deposit,withdrawal',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $savingsAccount = SavingsAccount::findOrFail($request->savings_account_id);

        if ($request->transaction_type === 'withdrawal' && $request->amount > $savingsAccount->balance) {
            return redirect()->back()->withErrors(['amount' => 'Insufficient balance for withdrawal.']);
        }

        // Calculate previous and new balance
        $previousBalance = $savingsAccount->balance;
        $newBalance = $request->transaction_type === 'deposit' 
            ? $previousBalance + $request->amount 
            : $previousBalance - $request->amount;

        // Update savings account balance
        $savingsAccount->balance = $newBalance;
        $savingsAccount->save();

        // Create transaction record
        Transaction::create([
            'transaction_number' => $this->generateTransactionNumber(),
            'member_id' => $savingsAccount->member_id,
            'branch_id' => $savingsAccount->branch_id,
            'processed_by' => Auth::check() ? Auth::user()->id : null,
            'transaction_type' => $request->transaction_type,
            'amount' => $request->amount,
            'previous_balance' => $previousBalance,
            'new_balance' => $newBalance,
            'description' => $request->description,
            'transaction_date' => now(),
            'reference_type' => 'App\Models\SavingsAccount',
            'reference_id' => $savingsAccount->id
        ]);

        return redirect()->route('admin.transactions')->with('success', 'Transaction processed successfully!');
    }
    
    /**
     * Generate a unique transaction number
     *
     * @return string
     */
    private function generateTransactionNumber()
    {
        $prefix = 'TXN';
        $uniqueId = strtoupper(Str::random(8));
        $timestamp = now()->format('YmdHis');
        
        return $prefix . $timestamp . $uniqueId;
    }
}