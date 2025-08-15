<?php

namespace App\Http\Controllers;

use App\Models\SavingsAccount;
use App\Models\SavingsType;
use App\Models\Member;
use App\Models\Branch;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SavingsController extends Controller
{
    public function index(Request $request)
    {
        $query = SavingsAccount::with(['member', 'savingsType', 'branch'])
            ->orderBy('created_at', 'desc');
            
        // Apply branch filter for non-super-admin users
        if (!Auth::user()->isSuperAdmin()) {
            $query->where('branch_id', Auth::user()->branch_id);
        }
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('account_number', 'like', "%{$search}%")
                  ->orWhereHas('member', function($mq) use ($search) {
                      $mq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('member_number', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('savings_type_id')) {
            $query->where('savings_type_id', $request->savings_type_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $accounts = $query->paginate(15);
        $savingsTypes = SavingsType::active()->get();
        $branches = Branch::active()->get();
        
        return view('savings.index', compact('accounts', 'savingsTypes', 'branches'));
    }
    
    public function create()
    {
        $members = Member::active()->kycVerified()->get();
        $savingsTypes = SavingsType::active()->get();
        $branches = Branch::active()->get();
        
        return view('savings.create', compact('members', 'savingsTypes', 'branches'));
    }
    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'member_id' => 'required|exists:members,id',
            'savings_type_id' => 'required|exists:savings_types,id',
            'branch_id' => 'required|exists:branches,id',
            'initial_deposit' => 'required|numeric|min:0',
        ]);
        
        $savingsType = SavingsType::find($validatedData['savings_type_id']);
        
        if ($validatedData['initial_deposit'] < $savingsType->min_balance) {
            return back()->withErrors(['initial_deposit' => 'Initial deposit must be at least ' . $savingsType->min_balance]);
        }
        
        DB::transaction(function () use ($validatedData, $savingsType) {
            $account = SavingsAccount::create([
                'account_number' => $this->generateAccountNumber(),
                'member_id' => $validatedData['member_id'],
                'savings_type_id' => $validatedData['savings_type_id'],
                'branch_id' => $validatedData['branch_id'],
                'balance' => $validatedData['initial_deposit'],
                'opened_date' => now(),
            ]);
            
            // Create initial deposit transaction
            if ($validatedData['initial_deposit'] > 0) {
                Transaction::create([
                    'transaction_number' => $this->generateTransactionNumber(),
                    'member_id' => $validatedData['member_id'],
                    'branch_id' => $validatedData['branch_id'],
                    'transaction_type' => 'deposit',
                    'amount' => $validatedData['initial_deposit'],
                    'balance_before' => 0,
                    'balance_after' => $validatedData['initial_deposit'],
                    'reference_type' => 'savings_account',
                    'reference_id' => $account->id,
                    'description' => "Initial deposit for account #{$account->account_number}",
                    'processed_by' => Auth::id(),
                    'transaction_date' => now(),
                ]);
            }
        });
        
        return redirect()->route('savings.index')
            ->with('success', 'Savings account created successfully.');
    }
    
    public function show(SavingsAccount $savingsAccount)
    {
        $savingsAccount->load(['member', 'savingsType', 'branch', 'transactions.processedBy']);
        return view('savings.show', compact('savingsAccount'));
    }
    
    public function deposit(Request $request, SavingsAccount $account)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
        ]);
        
        DB::transaction(function () use ($request, $account) {
            $previousBalance = $account->balance;
            $newBalance = $previousBalance + $request->amount;
            
            $account->update(['balance' => $newBalance]);
            
            Transaction::create([
                'transaction_number' => $this->generateTransactionNumber(),
                'member_id' => $account->member_id,
                'branch_id' => $account->branch_id,
                'transaction_type' => 'deposit',
                'amount' => $request->amount,
                'balance_before' => $previousBalance,
                'balance_after' => $newBalance,
                'reference_type' => 'savings_account',
                'reference_id' => $account->id,
                'description' => $request->description ?: "Deposit to account #{$account->account_number}",
                'processed_by' => Auth::id(),
                'transaction_date' => now(),
            ]);
        });
        
        return back()->with('success', 'Deposit processed successfully.');
    }
    
    public function withdraw(Request $request, SavingsAccount $account)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
        ]);
        
        $savingsType = $account->savingsType;
        
        // Check if withdrawal amount exceeds balance
        if ($request->amount > $account->balance) {
            return back()->withErrors(['amount' => 'Insufficient balance.']);
        }
        
        // Check if remaining balance meets minimum requirement
        $remainingBalance = $account->balance - $request->amount;
        if ($remainingBalance < $savingsType->min_balance) {
            return back()->withErrors(['amount' => 'Withdrawal would result in balance below minimum required.']);
        }
        
        // Check withdrawal limits (if applicable)
        if ($savingsType->withdrawal_limit_amount) {
            $monthlyWithdrawals = Transaction::where('reference_type', 'savings_account')
                ->where('reference_id', $account->id)
                ->where('transaction_type', 'withdrawal')
                ->whereMonth('transaction_date', now()->month)
                ->sum('amount');
                
            if (($monthlyWithdrawals + $request->amount) > $savingsType->withdrawal_limit_amount) {
                return back()->withErrors(['amount' => 'Monthly withdrawal limit exceeded.']);
            }
        }
        
        DB::transaction(function () use ($request, $account) {
            $previousBalance = $account->balance;
            $newBalance = $previousBalance - $request->amount;
            
            $account->update(['balance' => $newBalance]);
            
            Transaction::create([
                'transaction_number' => $this->generateTransactionNumber(),
                'member_id' => $account->member_id,
                'branch_id' => $account->branch_id,
                'transaction_type' => 'withdrawal',
                'amount' => $request->amount,
                'balance_before' => $previousBalance,
                'balance_after' => $newBalance,
                'reference_type' => 'savings_account',
                'reference_id' => $account->id,
                'description' => $request->description ?: "Withdrawal from account #{$account->account_number}",
                'processed_by' => Auth::id(),
                'transaction_date' => now(),
            ]);
        });
        
        return back()->with('success', 'Withdrawal processed successfully.');
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