<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Branch;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::with('branch')->paginate(15);
        return view('admin.members.index', compact('members'));
    }

    public function create()
    {
        $branches = Branch::all();
        return view('admin.members.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|string|unique:members',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:members',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'occupation' => 'nullable|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'citizenship_number' => 'nullable|string|max:20',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'spouse_name' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        Member::create($request->all());

        return redirect()->route('admin.members')->with('success', 'Member created successfully!');
    }

    public function show(Member $member)
    {
        $member->load(['branch', 'loans', 'savingsAccounts']);
        return view('admin.members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        $branches = Branch::all();
        return view('admin.members.edit', compact('member', 'branches'));
    }

    public function update(Request $request, Member $member)
    {
        $request->validate([
            'member_id' => 'required|string|unique:members,member_id,' . $member->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:members,email,' . $member->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'occupation' => 'nullable|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'citizenship_number' => 'nullable|string|max:20',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'spouse_name' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        $member->update($request->all());

        return redirect()->route('admin.members')->with('success', 'Member updated successfully!');
    }

    public function destroy(Member $member)
    {
        $member->delete();
        return redirect()->route('admin.members')->with('success', 'Member deleted successfully!');
    }
}