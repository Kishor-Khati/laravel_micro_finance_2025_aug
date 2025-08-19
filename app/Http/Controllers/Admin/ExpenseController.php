<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with(['branch'])->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.expenses.index', compact('expenses'));
    }

    public function create()
    {
        $branches = Branch::all();
        return view('admin.expenses.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'branch_id' => 'required|exists:branches,id',
            'vendor_name' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:100',
            'receipt_image' => 'nullable|string|max:255',
            'status' => 'required|in:pending,approved,rejected',
            'approval_remarks' => 'nullable|string',
        ]);

        $data = $request->all();
        
        if (Auth::check()) {
            $data['requested_by'] = Auth::user()->id;
        } else {
            $data['requested_by'] = null;
        }
        
        Expense::create($data);

        return redirect()->route('admin.expenses.index')->with('success', 'Expense recorded successfully!');
    }

    public function show(Expense $expense)
    {
        $expense->load(['branch']);
        return view('admin.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $branches = Branch::all();
        return view('admin.expenses.edit', compact('expense', 'branches'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
            'vendor_name' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:100',
            'receipt_image' => 'nullable|string|max:255',
            'status' => 'required|in:pending,approved,rejected',
            'approval_remarks' => 'nullable|string',
        ]);

        $data = $request->all();
        
        // Use AD date for database storage (from hidden _ad field)
        if (isset($data['expense_date_ad'])) {
            $data['expense_date'] = $data['expense_date_ad'];
            unset($data['expense_date_ad']);
        }
        
        $expense->update($data);

        return redirect()->route('admin.expenses.show', $expense)->with('success', 'Expense updated successfully!');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('admin.expenses.index')->with('success', 'Expense deleted successfully!');
    }
}