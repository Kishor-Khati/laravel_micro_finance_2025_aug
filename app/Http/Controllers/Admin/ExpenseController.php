<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Branch;
use Illuminate\Http\Request;

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
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'branch_id' => 'required|exists:branches,id',
            'payment_method' => 'required|in:cash,bank_transfer,cheque,online',
            'receipt_number' => 'nullable|string|max:100',
            'vendor_name' => 'nullable|string|max:255',
        ]);

        Expense::create($request->all());

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
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'branch_id' => 'required|exists:branches,id',
            'payment_method' => 'required|in:cash,bank_transfer,cheque,online',
            'receipt_number' => 'nullable|string|max:100',
            'vendor_name' => 'nullable|string|max:255',
        ]);

        $expense->update($request->all());

        return redirect()->route('admin.expenses.show', $expense)->with('success', 'Expense updated successfully!');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('admin.expenses.index')->with('success', 'Expense deleted successfully!');
    }
}