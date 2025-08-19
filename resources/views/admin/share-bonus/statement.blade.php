@extends('layouts.admin')

@section('title', 'Share Bonus Statement')
@section('subtitle', 'Share bonus statement with detailed distribution')

@section('content')
<div class="space-y-6">
    <!-- Statement Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Share Bonus Statement</h3>
            <div class="flex space-x-2">
                <a href="{{ route('admin.share-bonus.export-pdf', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d'), 'branch_id' => request('branch_id')]) }}" 
                   class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </a>
                <a href="{{ route('admin.share-bonus.export-excel', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d'), 'branch_id' => request('branch_id')]) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                </a>
                <a href="{{ route('admin.share-bonus.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg text-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="bg-gray-50 p-3 rounded-lg">
                <p class="text-sm text-gray-500">Period</p>
                <p class="font-medium">{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
            </div>
            
            <div class="bg-gray-50 p-3 rounded-lg">
                <p class="text-sm text-gray-500">Branch</p>
                <p class="font-medium">{{ request('branch_id') ? \App\Models\Branch::find(request('branch_id'))->name : 'All Branches' }}</p>
            </div>
            
            <div class="bg-gray-50 p-3 rounded-lg">
                <p class="text-sm text-gray-500">Generated On</p>
                <p class="font-medium">{{ now()->format('M d, Y') }} {{ now()->format('H:i') }}</p>
            </div>
        </div>
    </div>
    
    <!-- Financial Summary -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Financial Summary</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-sm text-blue-500">Raw Income</p>
                <p class="text-2xl font-bold text-blue-700">{{ number_format($data['total_raw_income'], 2) }}</p>
                <p class="text-xs text-blue-500 mt-1">Total loan interest collected</p>
            </div>
            
            <div class="bg-green-50 p-4 rounded-lg">
                <p class="text-sm text-green-500">Share Bonus ({{ number_format((request('share_bonus_percentage') ?? 30), 2) }}%)</p>
                <p class="text-2xl font-bold text-green-700">{{ number_format($data['share_bonus']['total_share_bonus'], 2) }}</p>
                <p class="text-xs text-green-500 mt-1">Distributed to members</p>
            </div>
            
            <div class="bg-red-50 p-4 rounded-lg">
                <p class="text-sm text-red-500">Expenses</p>
                <p class="text-2xl font-bold text-red-700">{{ number_format($data['total_expenses'], 2) }}</p>
                <p class="text-xs text-red-500 mt-1">Total operational costs</p>
            </div>
            
            <div class="bg-purple-50 p-4 rounded-lg">
                <p class="text-sm text-purple-500">Net Income</p>
                <p class="text-2xl font-bold text-purple-700">{{ number_format($data['final_balance'], 2) }}</p>
                <p class="text-xs text-purple-500 mt-1">Raw income after expenses</p>
            </div>
        </div>
    </div>
    
    <!-- Share Bonus Distribution -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Share Bonus Distribution</h3>
            <div class="flex space-x-3">
                <button type="button" id="selectAllBtn" onclick="selectAllMembers()" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm">
                    <i class="fas fa-check-square mr-1"></i> Select All
                </button>
                <button type="button" id="unselectAllBtn" onclick="unselectAllMembers()" 
                    class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg text-sm">
                    <i class="fas fa-square mr-1"></i> Unselect All
                </button>
                <button type="button" id="applyBonusBtn" onclick="applySelectedBonuses()" 
                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm" disabled>
                    <i class="fas fa-plus-circle mr-1"></i> Apply Bonuses
                </button>
                <button type="button" onclick="undoShareBonuses()" 
                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm">
                    <i class="fas fa-undo mr-1"></i> Undo Bonuses
                </button>
            </div>
        </div>
        
        <p class="text-gray-600 mb-4">Share bonuses are distributed proportionally based on members' savings account balances. Select members to apply bonuses to their accounts.</p>
        
        <form id="applyBonusForm" action="{{ route('admin.share-bonus.apply') }}" method="POST">
            @csrf
            <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
            <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
            <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
            <input type="hidden" name="share_bonus_percentage" value="{{ request('share_bonus_percentage') ?? 30 }}">
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleAllMembers()" 
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Number</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member Name</th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Savings Balance</th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Proportion</th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Bonus Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($data['share_bonus']['member_bonuses'] as $index => $bonus)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="selected_members[]" value="{{ $bonus['account_number'] }}" 
                                    class="member-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" 
                                    onchange="updateApplyButton()" 
                                    data-bonus-amount="{{ $bonus['bonus_amount'] }}" 
                                    data-account-number="{{ $bonus['account_number'] }}">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $bonus['account_number'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $bonus['member_name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($bonus['savings_balance'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($bonus['proportion'] * 100, 2) }}%</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($bonus['bonus_amount'], 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" colspan="3">Total Selected:</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right" id="selectedCount">0 members</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right" id="selectedTotal">0.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    
    <!-- Additional Financial Details -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Loans Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Loans Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Loans:</span>
                    <span class="font-medium">{{ $data['loans']['total_loans'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Amount:</span>
                    <span class="font-medium">{{ number_format($data['loans']['total_amount'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Interest:</span>
                    <span class="font-medium">{{ number_format($data['loans']['total_interest'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Active Loans:</span>
                    <span class="font-medium">{{ $data['loans']['active_loans'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Active Amount:</span>
                    <span class="font-medium">{{ number_format($data['loans']['active_amount'], 2) }}</span>
                </div>
            </div>
        </div>
        
        <!-- Savings Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Savings Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Accounts:</span>
                    <span class="font-medium">{{ $data['savings']['total_accounts'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Balance:</span>
                    <span class="font-medium">{{ number_format($data['savings']['total_balance'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Active Accounts:</span>
                    <span class="font-medium">{{ $data['savings']['active_accounts'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Active Balance:</span>
                    <span class="font-medium">{{ number_format($data['savings']['active_balance'], 2) }}</span>
                </div>
            </div>
        </div>
        
        <!-- Transactions Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Transactions Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Deposits:</span>
                    <span class="font-medium">{{ number_format($data['transactions']['deposits'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Withdrawals:</span>
                    <span class="font-medium">{{ number_format($data['transactions']['withdrawals'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Loan Disbursements:</span>
                    <span class="font-medium">{{ number_format($data['transactions']['loan_disbursements'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Loan Payments:</span>
                    <span class="font-medium">{{ number_format($data['transactions']['loan_payments'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Transactions:</span>
                    <span class="font-medium">{{ $data['transactions']['total_transactions'] }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function selectAllMembers() {
    const checkboxes = document.querySelectorAll('.member-checkbox');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    selectAllCheckbox.checked = true;
    updateApplyButton();
}

function unselectAllMembers() {
    const checkboxes = document.querySelectorAll('.member-checkbox');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    selectAllCheckbox.checked = false;
    updateApplyButton();
}

function toggleAllMembers() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.member-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    updateApplyButton();
}

function updateApplyButton() {
    const checkboxes = document.querySelectorAll('.member-checkbox:checked');
    const applyButton = document.getElementById('applyBonusBtn');
    const selectedCount = document.getElementById('selectedCount');
    const selectedTotal = document.getElementById('selectedTotal');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    
    // Update apply button state
    applyButton.disabled = checkboxes.length === 0;
    
    // Calculate totals
    let totalAmount = 0;
    checkboxes.forEach(checkbox => {
        totalAmount += parseFloat(checkbox.dataset.bonusAmount);
    });
    
    // Update display
    selectedCount.textContent = checkboxes.length + ' members';
    selectedTotal.textContent = totalAmount.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    
    // Update select all checkbox state
    const allCheckboxes = document.querySelectorAll('.member-checkbox');
    if (checkboxes.length === 0) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = false;
    } else if (checkboxes.length === allCheckboxes.length) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = true;
    } else {
        selectAllCheckbox.indeterminate = true;
    }
}

function applySelectedBonuses() {
    const checkboxes = document.querySelectorAll('.member-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Please select at least one member to apply bonuses.');
        return;
    }
    
    const memberCount = checkboxes.length;
    let totalAmount = 0;
    checkboxes.forEach(checkbox => {
        totalAmount += parseFloat(checkbox.dataset.bonusAmount);
    });
    
    const confirmMessage = `Are you sure you want to apply share bonuses to ${memberCount} selected members?\n\nTotal amount: Rs. ${totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}\n\nThis action will add the bonus amounts to their savings accounts.`;
    
    if (confirm(confirmMessage)) {
        // Show loading state
        const applyButton = document.getElementById('applyBonusBtn');
        const originalText = applyButton.innerHTML;
        applyButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Applying...';
        applyButton.disabled = true;
        
        // Submit the form
        document.getElementById('applyBonusForm').submit();
    }
}

function undoShareBonuses() {
    const startDate = document.querySelector('input[name="start_date"]').value;
    const endDate = document.querySelector('input[name="end_date"]').value;
    const branchId = document.querySelector('input[name="branch_id"]').value;
    const shareBonusPercentage = document.querySelector('input[name="share_bonus_percentage"]').value;
    
    const confirmMessage = `Are you sure you want to undo all share bonuses for the period ${startDate} to ${endDate}?\n\nThis will reverse all bonus transactions applied during this period.`;
    
    if (confirm(confirmMessage)) {
        // Create a form to submit the undo request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.share-bonus.undo") }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add form data
        const fields = [
            {name: 'start_date', value: startDate},
            {name: 'end_date', value: endDate},
            {name: 'branch_id', value: branchId},
            {name: 'share_bonus_percentage', value: shareBonusPercentage}
        ];
        
        fields.forEach(field => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = field.name;
            input.value = field.value;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateApplyButton();
});
</script>
@endpush