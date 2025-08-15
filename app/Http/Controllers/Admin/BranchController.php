<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-branches')->only(['index', 'show']);
        $this->middleware('permission:create-branches')->only(['create', 'store']);
        $this->middleware('permission:edit-branches')->only(['edit', 'update']);
        $this->middleware('permission:delete-branches')->only(['destroy']);
    }
    
    public function index()
    {
        $branches = Branch::withCount(['users', 'members'])->paginate(10);
        return view('admin.branches.index', compact('branches'));
    }

    public function create()
    {
        return view('admin.branches.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:branches',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
        ]);

        Branch::create($request->all());

        return redirect()->route('admin.branches')->with('success', 'Branch created successfully!');
    }

    public function show(Branch $branch)
    {
        $branch->load(['users', 'members']);
        return view('admin.branches.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        return view('admin.branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:branches,code,' . $branch->id,
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
        ]);

        $branch->update($request->all());

        return redirect()->route('admin.branches')->with('success', 'Branch updated successfully!');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('admin.branches')->with('success', 'Branch deleted successfully!');
    }
}