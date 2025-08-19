@extends('layouts.admin')

@section('title', 'Loan Details')
@section('subtitle', 'View loan information and payment schedule')

@section('content')
<div class="space-y-6">
    <!-- Loan Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $loan->loanType->name }}</h3>
                <p class="text-gray-600">Loan ID: #{{ $loan->id }}</p>
                <p class="text-gray-700 mt-1">{{ $loan->member->first_name }} {{ $loan->member->last_name }} ({{ $loan->member->member_id }})</p>
                
                <div class="mt-4">
                    <span class="px-3 py-1 text-sm font-semibold rounded-full 
                        @switch($loan->status)
                            @case('pending') bg-yellow-100 text-yellow-800 @break
                            @case('approved') bg-blue-100 text-blue-800 @break
                            @case('active') bg-green-100 text-green-800 @break
                            @case('closed') bg-gray-100 text-gray-800 @break
                            @case('defaulted') bg-red-100 text-red-800 @break
                        @endswitch">
                        {{ ucfirst($loan->status) }}
                    </span>
                </div>
            </div>
            
            <div class="flex space-x-2">
                <a href="{{ route('admin.loans.edit', $loan) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                @if($loan->installments->where('status', 'overdue')->count() > 0 || $loan->installments->where('status', 'pending')->where('due_date', '<', now())->count() > 0)
                    <button onclick="calculatePenalties()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-calculator mr-2"></i> Calculate Penalties
                    </button>
                    <button onclick="waiveAllPenalties()" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-hand-paper mr-2"></i> Waive All Penalties
                    </button>
                @endif
                <a href="{{ route('admin.loans.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Loan Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Loan Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Loan Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500">Principal Amount</p>
                    <p class="text-xl font-bold text-gray-900">रू {{ number_format($loan->approved_amount ?? $loan->requested_amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Interest Rate</p>
                    <p class="font-medium">{{ $loan->interest_rate }}% per annum</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Loan Term</p>
                    <p class="font-medium">{{ $loan->duration_months }} months</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Monthly Payment</p>
                    <p class="font-medium text-blue-600">रू {{ $loan->duration_months > 0 ? number_format((($loan->approved_amount ?? $loan->requested_amount) + (($loan->approved_amount ?? $loan->requested_amount) * $loan->interest_rate / 100)) / $loan->duration_months, 2) : '0.00' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Interest</p>
                    <p class="font-medium">रू {{ number_format(($loan->approved_amount ?? $loan->requested_amount) * $loan->interest_rate / 100, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Amount</p>
                    <p class="font-medium text-green-600">रू {{ number_format(($loan->approved_amount ?? $loan->requested_amount) + (($loan->approved_amount ?? $loan->requested_amount) * $loan->interest_rate / 100), 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Member Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Member Details</h3>
            <div class="flex items-center mb-4">
                <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center mr-4">
                    <i class="fas fa-user text-gray-600 text-xl"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">{{ $loan->member->first_name }} {{ $loan->member->last_name }}</p>
                    <p class="text-sm text-gray-600">{{ $loan->member->member_id }}</p>
                </div>
            </div>
            
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Phone:</span>
                    <span class="text-sm font-medium">{{ $loan->member->phone }}</span>
                </div>
                
                @if($loan->member->email)
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Email:</span>
                    <span class="text-sm font-medium">{{ $loan->member->email }}</span>
                </div>
                @endif
                
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Branch:</span>
                    <span class="text-sm font-medium">{{ $loan->member->branch->name }}</span>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t">
                <a href="{{ route('admin.members.show', $loan->member) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View Member Profile →
                </a>
            </div>
        </div>

        <!-- Loan Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Summary</h3>
            
            @php
                $totalInstallments = $loan->installments->count();
                $paidInstallments = $loan->installments->where('status', 'paid')->count();
                $pendingInstallments = $loan->installments->where('status', 'pending')->count();
                $overdueInstallments = $loan->installments->where('status', 'overdue')->count();
                $totalPaid = $loan->installments->where('status', 'paid')->sum('amount');
                $totalPending = $loan->installments->where('status', '!=', 'paid')->sum('amount');
            @endphp

            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Total Installments:</span>
                    <span class="font-medium">{{ $totalInstallments }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Paid:</span>
                    <span class="font-medium text-green-600">{{ $paidInstallments }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Pending:</span>
                    <span class="font-medium text-yellow-600">{{ $pendingInstallments }}</span>
                </div>
                @if($overdueInstallments > 0)
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Overdue:</span>
                    <span class="font-medium text-red-600">{{ $overdueInstallments }}</span>
                </div>
                @endif
            </div>

            <div class="mt-4 pt-4 border-t space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Amount Paid:</span>
                    <span class="font-medium text-green-600">रू {{ number_format($totalPaid, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Amount Pending:</span>
                    <span class="font-medium text-yellow-600">रू {{ number_format($totalPending, 2) }}</span>
                </div>
            </div>

            <!-- Progress Bar -->
            @php
                $progressPercentage = $totalInstallments > 0 ? ($paidInstallments / $totalInstallments) * 100 : 0;
            @endphp
            <div class="mt-4">
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Progress</span>
                    <span>{{ number_format($progressPercentage, 1) }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $progressPercentage }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loan Purpose -->
    @if($loan->purpose)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-3">Loan Purpose</h3>
        <p class="text-gray-700">{{ $loan->purpose }}</p>
    </div>
    @endif

    <!-- Installment Schedule -->
    @if($loan->installments->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Payment Schedule</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penalty</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($loan->installments->sortBy('installment_number') as $installment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $installment->installment_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $installment->due_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            रू {{ number_format($installment->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($installment->penalty_amount > 0)
                                <span class="text-red-600 font-semibold">
                                    रू {{ number_format($installment->penalty_amount, 2) }}
                                </span>
                                @if($installment->penalty_waived)
                                    <span class="text-xs text-gray-500 block">(Waived)</span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            रू {{ number_format($installment->getTotalAmountWithPenalty(), 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @switch($installment->status)
                                    @case('paid') bg-green-100 text-green-800 @break
                                    @case('pending') bg-yellow-100 text-yellow-800 @break
                                    @case('overdue') bg-red-100 text-red-800 @break
                                    @default bg-gray-100 text-gray-800
                                @endswitch">
                                {{ ucfirst($installment->status) }}
                            </span>
                            @if($installment->isOverdue())
                                <span class="text-xs text-red-500 block">{{ $installment->calculateDaysOverdue() }} days overdue</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $installment->paid_date ? $installment->paid_date->format('M d, Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($installment->status !== 'paid')
                                <button class="text-green-600 hover:text-green-900 mr-2" onclick="markAsPaid({{ $installment->id }})">
                                    Mark Paid
                                </button>
                                @if($installment->penalty_amount > 0 && !$installment->penalty_waived)
                                    <button class="text-orange-600 hover:text-orange-900 mr-2" onclick="waivePenalty({{ $installment->id }})">
                                        Waive Penalty
                                    </button>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<script>
function markAsPaid(installmentId) {
    if (confirm('Mark this installment as paid?')) {
        // Implementation for marking installment as paid
        // This would typically make an AJAX call to update the installment
        console.log('Mark installment ' + installmentId + ' as paid');
    }
}

function calculatePenalties() {
    if (confirm('Are you sure you want to calculate penalties for all overdue installments?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.loans.calculate-penalties") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function waiveAllPenalties() {
    const reason = prompt('Please enter the reason for waiving all penalties:');
    if (reason && reason.trim() !== '') {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.loans.waive-penalties", $loan) }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        const reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'reason';
        reasonInput.value = reason;
        form.appendChild(reasonInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function waivePenalty(installmentId) {
    const reason = prompt('Please enter the reason for waiving this penalty:');
    if (reason && reason.trim() !== '') {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/installments/${installmentId}/waive-penalty`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        const reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'reason';
        reasonInput.value = reason;
        form.appendChild(reasonInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection