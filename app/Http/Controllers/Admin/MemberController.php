<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Branch;
use App\Exports\IndividualMemberExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('member_number', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('phone_secondary', 'like', "%{$search}%")
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
        $existingMembers = Member::active()->get(); // For family members selection
        return view('admin.members.create', compact('branches', 'existingMembers'));
    }

    public function store(Request $request)
    {
        // Handle member number generation
        $memberNumberData = $this->handleMemberNumber($request);
        
        $validationRules = [
            // Always validate member_number for uniqueness, regardless of auto-generation
            'member_number' => 'required|string|unique:members,member_number',
            'member_number_auto_generated' => 'boolean',
            'full_name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:255', // Keep for backward compatibility
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:members',
            'phone' => 'required|string|max:20',
            'phone_secondary' => 'nullable|string|max:20',
            'address' => 'required|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'required|in:male,female,other',
            'citizenship_number' => 'nullable|string|unique:members',
            'occupation' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
            'branch_id' => 'required|exists:branches,id',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_relation' => 'nullable|string|max:255',
            'family_members' => 'nullable|array',
            'family_members.*' => 'nullable|exists:members,id',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'kyc_documents' => 'nullable|array',
            'kyc_documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'status' => 'nullable|in:active,inactive,suspended,kyc_pending',
            'kyc_status' => 'nullable|in:pending,verified,rejected',
            'membership_date' => 'nullable|date',
        ];
        
        $validatedData = $request->validate($validationRules);
        
        // Set defaults
        $validatedData['member_number'] = $memberNumberData['member_number'];
        $validatedData['member_number_auto_generated'] = $memberNumberData['auto_generated'];
        $validatedData['membership_date'] = $validatedData['membership_date'] ?? now()->format('Y-m-d');
        $validatedData['status'] = $validatedData['status'] ?? 'active';
        $validatedData['kyc_status'] = $validatedData['kyc_status'] ?? 'pending';
        
        // Handle file uploads
        $validatedData = $this->handleFileUploads($request, $validatedData);
        
        try {
            Member::create($validatedData);
            return redirect()->route('admin.members.index')->with('success', 'Member created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create member: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Member $member)
    {
        $member->load(['branch', 'loans', 'savingsAccounts']);
        
        // Load family members if they exist
        $familyMembers = collect();
        if ($member->family_members && is_array($member->family_members)) {
            $familyMembers = Member::whereIn('id', $member->family_members)->get();
        }
        
        return view('admin.members.show', compact('member', 'familyMembers'));
    }

    public function edit(Member $member)
    {
        $branches = Branch::all();
        $existingMembers = Member::where('id', '!=', $member->id)->active()->get(); // Exclude current member
        return view('admin.members.edit', compact('member', 'branches', 'existingMembers'));
    }

    public function update(Request $request, Member $member)
    {
        // Handle member number generation
        $memberNumberData = $this->handleMemberNumber($request, $member);
        
        $validationRules = [
            // Always validate member_number for uniqueness, excluding current member
            'member_number' => 'required|string|unique:members,member_number,' . $member->id,
            'member_number_auto_generated' => 'boolean',
            'full_name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:members,email,' . $member->id,
            'phone' => 'required|string|max:20',
            'phone_secondary' => 'nullable|string|max:20',
            'address' => 'required|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'required|in:male,female,other',
            'citizenship_number' => 'nullable|string|unique:members,citizenship_number,' . $member->id,
            'occupation' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
            'branch_id' => 'required|exists:branches,id',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_relation' => 'nullable|string|max:255',
            'family_members' => 'nullable|array',
            'family_members.*' => 'nullable|exists:members,id',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'kyc_documents' => 'nullable|array',
            'kyc_documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'status' => 'required|in:active,inactive,suspended,kyc_pending',
            'kyc_status' => 'required|in:pending,verified,rejected',
            'membership_date' => 'required|date',
        ];
        
        $validatedData = $request->validate($validationRules);
        
        // Set member number data
        $validatedData['member_number'] = $memberNumberData['member_number'];
        $validatedData['member_number_auto_generated'] = $memberNumberData['auto_generated'];
        
        // Handle file uploads
        $validatedData = $this->handleFileUploads($request, $validatedData, $member);
        
        try {
            $member->update($validatedData);
            return redirect()->route('admin.members.index')->with('success', 'Member updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update member: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Member $member)
    {
        try {
            // Delete profile image from public/images/member-img
            if ($member->profile_image) {
                $imagePath = public_path('images/member-img/' . $member->profile_image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            // Delete KYC documents from public/images/kyc-docs
            if ($member->kyc_documents) {
                foreach ($member->kyc_documents as $filename) {
                    $filePath = public_path('images/kyc-docs/' . $filename);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }
            
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

    /**
     * Generate member number for AJAX request
     */
    public function generateNumber($branchId)
    {
        try {
            $branch = Branch::findOrFail($branchId);
            
            // Generate unique member number with retry logic
            $memberNumber = Member::generateMemberNumber();
            $attempts = 0;
            $maxAttempts = 10;
            
            while (Member::where('member_number', $memberNumber)->exists() && $attempts < $maxAttempts) {
                $memberNumber = Member::generateMemberNumber();
                $attempts++;
            }
            
            if ($attempts >= $maxAttempts) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to generate unique member number. Please try again.'
                ], 500);
            }
            
            return response()->json([
                'success' => true,
                'member_number' => $memberNumber,
                'branch' => $branch->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate member number: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a specific KYC document
     */
    public function deleteKycDocument(Member $member, $index)
    {
        try {
            $kycDocuments = $member->kyc_documents ?? [];
            
            // Check if the index exists
            if (!isset($kycDocuments[$index])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document not found'
                ], 404);
            }
            
            // Get the filename and file path
            $filename = $kycDocuments[$index];
            $filePath = public_path('images/kyc-docs/' . $filename);
            
            // Delete the file from public directory
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            // Remove the document from the array
            unset($kycDocuments[$index]);
            
            // Reindex the array to maintain sequential indices
            $kycDocuments = array_values($kycDocuments);
            
            // Update the member record
            $member->update(['kyc_documents' => $kycDocuments]);
            
            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully',
                'remaining_documents' => count($kycDocuments)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle member number generation logic
     */
    private function handleMemberNumber(Request $request, Member $member = null)
    {
        $autoGenerate = $request->boolean('member_number_auto_generated', true);
        
        if ($autoGenerate) {
            // Generate new member number with uniqueness guarantee
            $memberNumber = Member::generateMemberNumber();
            
            // Ensure uniqueness with retry logic
            $attempts = 0;
            $maxAttempts = 10;
            
            while (Member::where('member_number', $memberNumber)->exists() && $attempts < $maxAttempts) {
                $memberNumber = Member::generateMemberNumber();
                $attempts++;
            }
            
            if ($attempts >= $maxAttempts) {
                throw new \Exception('Unable to generate unique member number after multiple attempts');
            }
        } else {
            // Use provided member number or fallback to current member number
            $memberNumber = $request->input('member_number');
            
            // If no manual number provided, use existing or generate new
            if (empty($memberNumber)) {
                if ($member && $member->member_number) {
                    $memberNumber = $member->member_number;
                } else {
                    $memberNumber = Member::generateMemberNumber();
                    // Ensure uniqueness for fallback generation
                    while (Member::where('member_number', $memberNumber)->exists()) {
                        $memberNumber = Member::generateMemberNumber();
                    }
                }
            }
        }
        
        return [
            'member_number' => $memberNumber,
            'auto_generated' => $autoGenerate
        ];
    }
    
    /**
     * Handle file uploads for profile image and KYC documents
     */
    private function handleFileUploads(Request $request, array $validatedData, Member $member = null)
    {
        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Create directory if it doesn't exist
            $imageDir = public_path('images/member-img');
            if (!file_exists($imageDir)) {
                mkdir($imageDir, 0755, true);
            }
            
            // Delete old profile image if updating
            if ($member && $member->profile_image) {
                $oldImagePath = public_path('images/member-img/' . $member->profile_image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            
            $profileImage = $request->file('profile_image');
            $filename = time() . '_' . $profileImage->getClientOriginalName();
            $profileImage->move($imageDir, $filename);
            $validatedData['profile_image'] = $filename;
        }
        
        // Handle KYC documents upload - IMPROVED VERSION
        if ($request->hasFile('kyc_documents')) {
            // Create directory if it doesn't exist
            $kycDir = public_path('images/kyc-docs');
            if (!file_exists($kycDir)) {
                mkdir($kycDir, 0755, true);
            }
            
            $kycDocuments = [];
            
            foreach ($request->file('kyc_documents') as $document) {
                $filename = time() . '_' . uniqid() . '_' . $document->getClientOriginalName();
                $document->move($kycDir, $filename);
                $kycDocuments[] = $filename;
            }
            
            // Always append to existing documents when updating
            if ($member && $member->kyc_documents) {
                $validatedData['kyc_documents'] = array_merge($member->kyc_documents, $kycDocuments);
            } else {
                $validatedData['kyc_documents'] = $kycDocuments;
            }
        }
        
        return $validatedData;
    }
}