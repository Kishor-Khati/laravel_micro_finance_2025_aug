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
        $transaction->load(['reference', 'member', 'branch', 'processedBy', 'savingsAccount.savingsType']);
        return view('admin.transactions.show', compact('transaction'));
    }

    public function create()
    {
        $savingsAccounts = SavingsAccount::with('member')->get();
        $members = \App\Models\Member::all();
        $branches = \App\Models\Branch::all();
        return view('admin.transactions.create', compact('savingsAccounts', 'members', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'branch_id' => 'required|exists:branches,id',
            'transaction_type' => 'required|in:deposit,withdrawal,loan_disbursement,loan_payment,interest_earned,fee_charge',
            'amount' => 'required|numeric|min:0.01',
            'interest_amount' => 'nullable|numeric|min:0',
            'reference_type' => 'nullable|string|max:255',
            'reference_id' => 'nullable|integer',
            'description' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
        ]);

        // Get member's savings account if transaction involves savings
        $savingsAccount = null;
        $previousBalance = 0;
        $newBalance = 0;
        
        if (in_array($request->transaction_type, ['deposit', 'withdrawal'])) {
            $savingsAccount = SavingsAccount::where('member_id', $request->member_id)
                ->where('branch_id', $request->branch_id)
                ->where('status', 'active')
                ->first();
                
            if (!$savingsAccount) {
                return redirect()->back()->withErrors(['member_id' => 'No active savings account found for this member.']);
            }
            
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
        }

        // Use AD date for database storage (from hidden _ad field)
        $transactionDate = $request->transaction_date_ad ?? $request->transaction_date;
        
        // Create transaction record
        Transaction::create([
            'transaction_number' => $this->generateTransactionNumber(),
            'member_id' => $request->member_id,
            'branch_id' => $request->branch_id,
            'processed_by' => Auth::check() ? Auth::user()->id : null,
            'transaction_type' => $request->transaction_type,
            'amount' => $request->amount,
            'interest_amount' => $request->interest_amount ?? 0,
            'balance_before' => $previousBalance,
            'balance_after' => $newBalance,
            'reference_type' => $request->reference_type,
            'reference_id' => $request->reference_id,
            'description' => $request->description,
            'transaction_date' => $transactionDate,
        ]);

        return redirect()->route('admin.transactions.index')->with('success', 'Transaction processed successfully!');
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