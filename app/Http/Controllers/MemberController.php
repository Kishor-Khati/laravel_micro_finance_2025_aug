<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::with(['branch'])
            ->orderBy('created_at', 'desc');
            
        // Apply branch filter for non-super-admin users
        if (!Auth::user()->isSuperAdmin()) {
            $query->where('branch_id', Auth::user()->branch_id);
        }
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('member_number', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $members = $query->paginate(15);
        $branches = Branch::active()->get();
        
        return view('members.index', compact('members', 'branches'));
    }
    
    public function create()
    {
        $branches = Branch::active()->get();
        return view('members.create', compact('branches'));
    }
    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'citizenship_number' => 'required|string|unique:members,citizenship_number',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'occupation' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
            'branch_id' => 'required|exists:branches,id',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_relation' => 'nullable|string|max:255',
            'kyc_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        
        // Generate member number
        $validatedData['member_number'] = $this->generateMemberNumber();
        $validatedData['membership_date'] = now();
        
        // Handle KYC document uploads
        if ($request->hasFile('kyc_documents')) {
            $documents = [];
            foreach ($request->file('kyc_documents') as $key => $file) {
                $path = $file->store('kyc_documents', 'public');
                $documents[$key] = $path;
            }
            $validatedData['kyc_documents'] = $documents;
        }
        
        $member = Member::create($validatedData);
        
        return redirect()->route('members.show', $member)
            ->with('success', 'Member created successfully.');
    }
    
    public function show(Member $member)
    {
        $member->load(['branch', 'loans.loanType', 'savingsAccounts.savingsType', 'transactions']);
        return view('members.show', compact('member'));
    }
    
    public function edit(Member $member)
    {
        $branches = Branch::active()->get();
        return view('members.edit', compact('member', 'branches'));
    }
    
    public function update(Request $request, Member $member)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'citizenship_number' => 'required|string|unique:members,citizenship_number,' . $member->id,
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'occupation' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
            'branch_id' => 'required|exists:branches,id',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_relation' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,suspended,kyc_pending',
            'kyc_status' => 'required|in:pending,verified,rejected',
        ]);
        
        $member->update($validatedData);
        
        return redirect()->route('members.show', $member)
            ->with('success', 'Member updated successfully.');
    }
    
    public function destroy(Member $member)
    {
        // Delete KYC documents
        if ($member->kyc_documents) {
            foreach ($member->kyc_documents as $document) {
                Storage::disk('public')->delete($document);
            }
        }
        
        $member->delete();
        
        return redirect()->route('members.index')
            ->with('success', 'Member deleted successfully.');
    }
    
    private function generateMemberNumber()
    {
        $lastMember = Member::orderBy('id', 'desc')->first();
        $nextNumber = $lastMember ? intval(substr($lastMember->member_number, -5)) + 1 : 1;
        return 'MEM' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}