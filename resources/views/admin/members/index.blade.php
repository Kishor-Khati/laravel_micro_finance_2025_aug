@extends('layouts.admin')

@section('title', 'Member Management')
@section('subtitle', 'Manage microfinance members')

@section('content')
<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center">
        <h3 class="text-2xl font-bold text-gray-800">All Members</h3>
        <a href="{{ route('admin.members.create') }}" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-bold rounded-lg text-base px-6 py-3 transition-colors duration-200">
            <i class="fas fa-plus mr-2"></i>
            Add Member
        </a>
    </div>

    <!-- Search and Filter Form -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
        <form method="GET" action="{{ route('admin.members.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block mb-2 text-base font-semibold text-gray-700">Search Members</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="Search by name, phone, email..." 
                       class="bg-gray-50 border border-gray-300 text-gray-800 text-base font-medium rounded-lg focus:ring-blue-400 focus:border-blue-500 block w-full p-3 transition-colors duration-200">
            </div>
            
            <div>
                <label for="branch_id" class="block mb-2 text-base font-semibold text-gray-700">Branch</label>
                <select name="branch_id" id="branch_id" class="bg-gray-50 border border-gray-300 text-gray-800 text-base font-medium rounded-lg focus:ring-blue-400 focus:border-blue-500 block w-full p-3 transition-colors duration-200">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="status" class="block mb-2 text-base font-semibold text-gray-700">Status</label>
                <select name="status" id="status" class="bg-gray-50 border border-gray-300 text-gray-800 text-base font-medium rounded-lg focus:ring-blue-400 focus:border-blue-500 block w-full p-3 transition-colors duration-200">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-bold rounded-lg text-base px-6 py-3 text-center transition-colors duration-200">
                    <i class="fas fa-search mr-2"></i>
                    Search
                </button>
                <a href="{{ route('admin.members.index') }}" class="py-3 px-6 text-base font-semibold text-gray-700 focus:outline-none bg-white rounded-lg border border-gray-300 hover:bg-gray-50 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 transition-colors duration-200">
                    <i class="fas fa-times mr-2"></i>
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Professional Table -->
    <div class="relative shadow-lg sm:rounded-lg">
        <!-- Table Search Input -->
        <div class="flex items-center justify-start pb-4 bg-white px-6 pt-6">
            <label for="table-search" class="sr-only">Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input type="text" id="table-search-members" class="block pt-3 ps-12 text-base font-medium text-gray-800 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-400 focus:border-blue-500 transition-colors duration-200" placeholder="Search for members">
            </div>
        </div>
        
        <!-- Professional Table - FIXED: Removed overflow-x-auto wrapper -->
        <table class="w-full text-base text-left rtl:text-right text-gray-700 bg-white" id="members-table" data-searchable="true">
            <thead class="text-sm text-gray-800 uppercase bg-white border-b-2 border-gray-200 sticky top-0 z-50 shadow-sm" id="table-header">
                <tr>
                    <th scope="col" class="px-6 py-5 text-lg font-bold text-gray-900 bg-white">
                        S.NO
                    </th>       
                    <th scope="col" class="px-6 py-3 text-[17px] font-bold text-gray-900 bg-white">
                        Member Details
                    </th>
                    <th scope="col" class="px-6 py-3 text-[17px] font-bold text-gray-900 bg-white">
                        Contact Information
                    </th>
                    <th scope="col" class="px-6 py-3 text-[17px] font-bold text-gray-900 bg-white">
                        Branch
                    </th>
                    <th scope="col" class="px-6 py-3 text-[17px] font-bold text-gray-900 text-center bg-white">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-[17px] font-bold text-gray-900 text-center bg-white">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $index => $member)
                <tr class="bg-white border-b border-gray-200 hover:bg-gray-50 transition-colors duration-200" data-search="{{ strtolower($member->first_name . ' ' . $member->last_name . ' ' . $member->member_number . ' ' . $member->phone . ' ' . $member->email . ' ' . ($member->branch->name ?? '')) }}">
                    <td class="px-6 py-3 text-center">
                        <span class="text-base font-bold text-gray-800">{{ $members->firstItem() + $index }}</span>
                    </td>
                    <td class="px-6 py-3">
                        <!-- In the member list display -->
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center mr-3">
                                @if($member->profile_image && file_exists(public_path('images/member-img/' . $member->profile_image)))
                                    <img src="{{ asset('images/member-img/' . $member->profile_image) }}" 
                                         alt="Profile" 
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-blue-500 flex items-center justify-center text-white font-semibold text-xs">
                                        {{ $member->avatar_initials }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $member->full_name ?? $member->first_name . ' ' . $member->last_name }}</div>
                                <div class="text-sm text-gray-500">ID: {{ $member->member_number }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3">
                        <div class="text-base font-semibold text-gray-900">{{ $member->phone }}</div>
                        @if($member->email)
                            <div class="text-base font-medium text-gray-600">{{ $member->email }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-3">
                        <span class="text-base font-bold text-gray-900">{{ $member->branch->name ?? 'N/A' }}</span>
                    </td>
                    <td class="px-6 py-3 text-center">
                        @php
                            $statusConfig = match($member->status ?? 'active') {
                                'active' => ['class' => 'bg-green-100 text-green-800 border border-green-300', 'text' => 'Active'],
                                'inactive' => ['class' => 'bg-gray-100 text-gray-800 border border-gray-300', 'text' => 'Inactive'],
                                'suspended' => ['class' => 'bg-red-100 text-red-800 border border-red-300', 'text' => 'Suspended'],
                                default => ['class' => 'bg-green-100 text-green-800 border border-green-300', 'text' => 'Active']
                            };
                        @endphp
                        <span class="{{ $statusConfig['class'] }} text-sm font-bold px-3 py-2 rounded-full">
                            {{ $statusConfig['text'] }}
                        </span>
                    </td>
                    <td class="px-6 py-3">
                        <div class="flex items-center justify-center space-x-4">
                            <a href="{{ route('admin.members.show', $member) }}" class="text-blue-600 hover:text-blue-800 transition-colors duration-200" title="View Details">
                                <i class="fas fa-eye text-lg"></i>
                            </a>
                            <a href="{{ route('admin.members.edit', $member) }}" class="text-amber-600 hover:text-amber-800 transition-colors duration-200" title="Edit Member">
                                <i class="fas fa-edit text-lg"></i>
                            </a>
                            <button type="button" onclick="confirmDelete('{{ route('admin.members.destroy', $member) }}', '{{ $member->first_name }} {{ $member->last_name }}')" class="text-red-600 hover:text-red-800 transition-colors duration-200" title="Delete Member">
                                <i class="fas fa-trash text-lg"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center bg-gray-50">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-users text-gray-400 text-5xl mb-6"></i>
                            <p class="text-xl font-bold text-gray-800">No members found</p>
                            <p class="text-base font-semibold text-gray-600 mt-3">
                                Get started by <a href="{{ route('admin.members.create') }}" class="text-blue-600 hover:text-blue-800 font-bold transition-colors duration-200">adding your first member</a>
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $members->links() }}
    </div>
</div>

<!-- Enhanced Search Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize table search
    const searchInput = document.getElementById('table-search-members');
    const table = document.getElementById('members-table');
    const rows = table.querySelectorAll('tbody tr');
    
    // Enhanced search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        let visibleRows = 0;
        
        rows.forEach(row => {
            const searchData = row.getAttribute('data-search');
            const isVisible = !searchTerm || (searchData && searchData.includes(searchTerm));
            
            if (isVisible) {
                row.style.display = '';
                visibleRows++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Show/hide "no results" message
        const noResultsRow = table.querySelector('tbody tr td[colspan]');
        if (noResultsRow && visibleRows === 0 && searchTerm) {
            noResultsRow.parentElement.style.display = '';
            noResultsRow.innerHTML = `
                <td colspan="6" class="px-6 py-16 text-center bg-gray-50">
                    <div class="flex flex-col items-center justify-center">
                        <i class="fas fa-search text-gray-400 text-5xl mb-6"></i>
                        <p class="text-xl font-bold text-gray-800">No members found for "${searchTerm}"</p>
                        <p class="text-base font-semibold text-gray-600 mt-3">Try adjusting your search terms</p>
                    </div>
                </td>
            `;
        }
    });
});

// Enhanced SweetAlert2 Delete Confirmation
function confirmDelete(url, memberName) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete ${memberName}. This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Create and submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
    // Simple sticky header - no JavaScript needed, pure CSS solution
    // The sticky positioning is handled by CSS classes: sticky top-0 z-50
    
    // Optional: Add scroll effect for enhanced visual feedback
    let lastScrollTop = 0;
    window.addEventListener('scroll', function() {
        const tableHeader = document.getElementById('table-header');
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (tableHeader) {
            if (scrollTop > 100) {
                // Add enhanced shadow when scrolling - removed blue border
                tableHeader.classList.add('shadow-lg', 'border-b-3', 'border-gray-300');
                tableHeader.classList.remove('shadow-sm', 'border-b-2', 'border-gray-200');
            } else {
                // Normal shadow when at top
                tableHeader.classList.remove('shadow-lg', 'border-b-3', 'border-gray-300');
                tableHeader.classList.add('shadow-sm', 'border-b-2', 'border-gray-200');
            }
        }
        lastScrollTop = scrollTop;
    });
</script>
@endsection

@push('scripts')
<script src="{{ asset('js/sweetalert.js') }}"></script>
@endpush