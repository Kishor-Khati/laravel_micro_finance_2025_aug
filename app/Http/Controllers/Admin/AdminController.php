<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Member;
use App\Models\Loan;
use App\Models\SavingsAccount;
use App\Models\Transaction;
use App\Models\Branch;
use App\Models\Expense;
use App\Models\LoanInstallment;
use App\Models\ShareBonus;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get members with savings for bonus highlighting
        $membersWithSavings = Member::whereHas('savingsAccounts', function($q) {
            $q->where('balance', '>', 0);
        })->get();
        
        // Calculate financial metrics
        $totalSavings = SavingsAccount::sum('balance');
        $totalExpenses = Expense::where('status', 'approved')->sum('amount');
        
        // Calculate raw income and net income
        $totalRawIncome = LoanInstallment::where('status', 'paid')->sum('interest_amount');
        $netIncome = $totalRawIncome - $totalExpenses; // Net income = Raw income - Expenses
        
        // Get total share bonus from stored entries (only approved ones)
        $totalShareBonus = ShareBonus::approved()->sum('amount');
        
        $availableBalance = $netIncome - $totalShareBonus; // Available balance = Net income - Share bonus
        $finalBalance = $netIncome; // Final balance is the net income
        
        $stats = [
            'total_users' => User::count(),
            'total_members' => Member::count(),
            'active_loans' => Loan::where('status', 'active')->count(),
            'total_savings' => $totalSavings,
            'total_branches' => Branch::count(),
            'recent_transactions' => Transaction::with(['member', 'savingsAccount.member'])->orderBy('created_at', 'desc')->take(5)->get(),
            'monthly_loans' => Loan::whereMonth('created_at', date('m'))->count(),
            'monthly_savings' => SavingsAccount::whereMonth('created_at', date('m'))->sum('balance'),
            'members_with_savings' => $membersWithSavings,
            'total_expenses' => $totalExpenses,
            'total_share_bonus' => $totalShareBonus,
            'final_balance' => $finalBalance,
            'available_balance' => $availableBalance,
            'total_raw_income' => $totalRawIncome
        ];

        return view('admin.dashboard', compact('stats'));
    }
    
    public function formDemo(Request $request)
    {
        // If the form is submitted, validate the inputs
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'age' => 'nullable|numeric|min:18|max:100',
                'country' => 'required|string',
                'bio' => 'nullable|string|max:1000',
                'gender' => 'required|in:male,female,other',
                'terms' => 'required|accepted',
            ]);
            
            return redirect()->route('admin.form-demo')
                ->with('success', 'Form submitted successfully!');
        }
        
        return view('components.form-demo');
    }
    
    /**
     * Display the form components documentation page
     *
     * @return \Illuminate\View\View
     */
    public function formDocumentation()
    {
        return view('components.form-documentation');
    }
    
    /**
     * Display the SweetAlert demo page
     *
     * @return \Illuminate\View\View
     */
    public function sweetAlertDemo()
    {
        return view('components.sweet-alert-demo');
    }

    public function sweetAlertDocumentation()
    {
        return view('components.sweet-alert-documentation');
    }

    public function users()
    {
        $users = User::with('branch')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        $branches = Branch::all();
        return view('admin.users.create', compact('branches'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:super_admin,branch_manager,field_officer,accountant,member',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'branch_id' => $request->branch_id,
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully!');
    }

    public function editUser(User $user)
    {
        $branches = Branch::all();
        return view('admin.users.edit', compact('user', 'branches'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:super_admin,branch_manager,field_officer,accountant,member',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $user->update($request->only(['name', 'email', 'role', 'branch_id']));

        return redirect()->route('admin.users')->with('success', 'User updated successfully!');
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted successfully!');
    }

    public function calendar()
    {
        return view('admin.calendar');
    }
}