<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Branch;
use App\Exports\IndividualMemberExport;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::with(['branch'])
            ->orderBy('created_at', 'desc');
            
        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('member_number', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Apply branch filter
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $members = $query->paginate(10)->withQueryString();
        $branches = Branch::active()->get();
        
        return view('admin.members.index', compact('members', 'branches'));
    }

    /**
     * Handle DataTables AJAX requests
     */
    public function dataTables(Request $request)
    {
        $query = Member::with('branch');
    
        // Handle search
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function($q) use ($searchValue) {
                $q->where('first_name', 'like', "%{$searchValue}%")
                  ->orWhere('last_name', 'like', "%{$searchValue}%")
                  ->orWhere('member_number', 'like', "%{$searchValue}%")
                  ->orWhere('phone', 'like', "%{$searchValue}%")
                  ->orWhere('email', 'like', "%{$searchValue}%")
                  ->orWhereHas('branch', function($branchQuery) use ($searchValue) {
                      $branchQuery->where('name', 'like', "%{$searchValue}%");
                  });
            });
        }
    
        // Get total count before any modifications
        $totalRecords = Member::count();
        
        // Clone query for filtered count before ordering
        $filteredRecords = (clone $query)->count();
    
        // Handle ordering
        if ($request->has('order')) {
            $orderColumn = $request->order[0]['column'];
            $orderDirection = $request->order[0]['dir'];
            
            // Fix column mapping to match DataTables column order
            $columns = ['first_name', 'phone', 'branch.name', 'status', 'actions'];
            
            if (isset($columns[$orderColumn])) {
                if ($columns[$orderColumn] === 'branch.name') {
                    $query->leftJoin('branches', 'members.branch_id', '=', 'branches.id')
                          ->orderBy('branches.name', $orderDirection)
                          ->select('members.*');
                } else {
                    $query->orderBy($columns[$orderColumn], $orderDirection);
                }
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
    
        // Handle pagination
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;
        $members = $query->skip($start)->take($length)->get();
    
        // Format data for DataTables
        $data = $members->map(function($member) {
            $statusClass = match($member->status ?? 'active') {
                'active' => 'bg-green-100 text-green-800',
                'inactive' => 'bg-gray-100 text-gray-800',
                'suspended' => 'bg-red-100 text-red-800',
                default => 'bg-green-100 text-green-800'
            };
    
            return [
                'member' => '<div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">' . e($member->first_name . ' ' . $member->last_name) . '</div>
                                    <div class="text-sm text-gray-500">ID: ' . e($member->member_number) . '</div>
                                </div>
                            </div>',
                'contact' => '<div class="text-sm text-gray-900">' . e($member->phone) . '</div>' . 
                           ($member->email ? '<div class="text-sm text-gray-500">' . e($member->email) . '</div>' : ''),
                'branch' => e($member->branch->name ?? 'N/A'),
                'status' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . $statusClass . '">' . 
                           ucfirst($member->status ?? 'Active') . '</span>',
                'actions' => '<a href="' . route('admin.members.show', $member) . '" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                             <a href="' . route('admin.members.edit', $member) . '" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                             <a href="#" class="delete-button text-red-600 hover:text-red-900" 
                                data-url="' . route('admin.members.destroy', $member) . '" 
                                data-name="' . e($member->first_name . ' ' . $member->last_name) . '">Delete</a>'
            ];
        });
    
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function create()
    {
        $branches = Branch::active()->get();
        return view('admin.members.create', compact('branches'));
    }

    public function store(Request $request)
    {
        // Set default membership_date to today if empty
        $data = $request->all();
        if (empty($data['membership_date'])) {
            $data['membership_date'] = now()->format('Y-m-d');
            $request->merge($data);
        }
        
        $request->validate([
            'member_number' => 'required|string|unique:members',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:members',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'required|in:male,female,other',
            'occupation' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
            'branch_id' => 'required|exists:branches,id',
            'citizenship_number' => 'required|string|unique:members',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_relation' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive,suspended,kyc_pending',
            'kyc_status' => 'nullable|in:pending,verified,rejected',
            'membership_date' => 'required|date',
        ]);

        try {
            Member::create($data);
            return redirect()->route('admin.members.index')->with('success', 'Member created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create member: ' . $e->getMessage())->withInput();
        }
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
        // Set default membership_date to existing value if empty
        $data = $request->all();
        if (empty($data['membership_date'])) {
            $data['membership_date'] = $member->membership_date->format('Y-m-d');
            $request->merge($data);
        }
        
        $request->validate([
            'member_number' => 'required|string|unique:members,member_number,' . $member->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:members,email,' . $member->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'required|in:male,female,other',
            'occupation' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
            'branch_id' => 'required|exists:branches,id',
            'citizenship_number' => 'required|string|unique:members,citizenship_number,' . $member->id,
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_relation' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,suspended,kyc_pending',
            'kyc_status' => 'required|in:pending,verified,rejected',
            'membership_date' => 'required|date',
        ]);

        try {
            $member->update($data);
            return redirect()->route('admin.members.index')->with('success', 'Member updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update member: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Member $member)
    {
        try {
            $member->delete();
            return redirect()->route('admin.members.index')->with('success', 'Member deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete member: ' . $e->getMessage());
        }
    }

    /**
     * Export individual member data to Excel
     */
    public function exportExcel(Member $member)
    {
        $member->load(['branch', 'loans.loanType', 'savingsAccounts.savingsType', 'transactions']);
        
        $filename = 'member_' . $member->member_id . '_' . date('Y-m-d') . '.xlsx';
        
        return Excel::download(new IndividualMemberExport($member), $filename);
    }

    /**
     * Export individual member data to PDF
     */
    public function exportPdf(Member $member)
    {
        $member->load(['branch', 'loans.loanType', 'savingsAccounts.savingsType', 'transactions']);
        
        $pdf = PDF::loadView('admin.members.pdf.individual', compact('member'));
        
        $filename = 'member_' . $member->member_id . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }
}