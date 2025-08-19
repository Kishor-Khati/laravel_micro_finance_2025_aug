@extends('layouts.admin')

@section('title', 'Penalty Statistics')
@section('subtitle', 'Overview of loan penalties and overdue payments')

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Overdue Installments -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Overdue Installments</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($statistics['total_overdue_installments']) }}</p>
                </div>
            </div>
        </div>

        <!-- Total Penalty Amount -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-orange-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Penalty Amount</p>
                    <p class="text-2xl font-bold text-gray-900">रू {{ number_format($statistics['total_penalty_amount'], 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Waived Penalties -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-hand-paper text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Waived Penalties</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($statistics['total_waived_penalties']) }}</p>
                </div>
            </div>
        </div>

        <!-- Average Penalty -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-calculator text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Average Penalty</p>
                    <p class="text-2xl font-bold text-gray-900">रू {{ number_format($statistics['average_penalty_per_installment'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Penalty Management Actions</h3>
        <div class="flex flex-wrap gap-4">
            <button onclick="calculateAllPenalties()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-calculator mr-2"></i> Calculate All Penalties
            </button>
            <a href="{{ route('admin.loans.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-list mr-2"></i> View All Loans
            </a>
            <button onclick="refreshStatistics()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-sync-alt mr-2"></i> Refresh Statistics
            </button>
        </div>
    </div>

    <!-- Penalty Information -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">How Penalties Work</h3>
        <div class="prose max-w-none">
            <ul class="list-disc list-inside space-y-2 text-gray-700">
                <li><strong>Default Penalty Rate:</strong> 0.1% per day on outstanding amount</li>
                <li><strong>Calculation:</strong> Penalties are calculated from the due date until payment</li>
                <li><strong>Automatic Calculation:</strong> Use the "Calculate All Penalties" button or run the command: <code class="bg-gray-100 px-2 py-1 rounded">php artisan penalties:calculate</code></li>
                <li><strong>Waiving Penalties:</strong> Penalties can be waived individually or for entire loans with proper authorization</li>
                <li><strong>Status Updates:</strong> Installments are automatically marked as "overdue" when penalties are calculated</li>
            </ul>
        </div>
    </div>

    <!-- Recent Overdue Installments -->
    @if(isset($recentOverdue) && $recentOverdue->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Overdue Installments</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loan ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Installment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penalty</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Overdue</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentOverdue as $installment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <a href="{{ route('admin.loans.show', $installment->loan) }}" class="text-blue-600 hover:text-blue-900">
                                #{{ $installment->loan_id }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $installment->loan->member->first_name }} {{ $installment->loan->member->last_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            #{{ $installment->installment_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $installment->due_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            रू {{ number_format($installment->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">
                            रू {{ number_format($installment->penalty_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $installment->calculateDaysOverdue() }} days
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
function calculateAllPenalties() {
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

function refreshStatistics() {
    window.location.reload();
}
</script>
@endsection