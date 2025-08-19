@extends('layouts.admin')

@section('title', 'Share Bonus Management')
@section('subtitle', 'Generate and manage share bonus statements')

@section('content')
<div class="space-y-6">
    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Statements</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalStatements }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-coins text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Bonus Distributed</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalBonusDistributed, 2) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Recent Statements</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $recentStatements->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtering Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Statements</h3>
        
        <form method="GET" action="{{ route('admin.share-bonus.index') }}" class="space-y-4">
            <!-- Date Range Options -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Range Options</label>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="setDateRange('all')" 
                            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 {{ !request('start_date') && !request('end_date') ? 'bg-blue-100 border-blue-300' : '' }}">
                        All Time
                    </button>
                    <button type="button" onclick="setDateRange('today')" 
                            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                        Today
                    </button>
                    <button type="button" onclick="setDateRange('this_week')" 
                            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                        This Week
                    </button>
                    <button type="button" onclick="setDateRange('this_month')" 
                            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                        This Month
                    </button>
                    <button type="button" onclick="setDateRange('last_month')" 
                            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                        Last Month
                    </button>
                    <button type="button" onclick="setDateRange('this_year')" 
                            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                        This Year
                    </button>
                    <button type="button" onclick="setDateRange('custom')" 
                            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 {{ request('start_date') || request('end_date') ? 'bg-blue-100 border-blue-300' : '' }}">
                        Custom Range
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="filter_start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" 
                           id="filter_start_date" 
                           name="start_date" 
                           value="{{ request('start_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="filter_end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" 
                           id="filter_end_date" 
                           name="end_date" 
                           value="{{ request('end_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="branch_id" class="block text-sm font-medium text-gray-700 mb-1">Branch</label>
                    <select id="branch_id" 
                            name="branch_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" 
                            name="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Statuses</option>
                        <option value="generated" {{ request('status') == 'generated' ? 'selected' : '' }}>Generated</option>
                        <option value="partially_applied" {{ request('status') == 'partially_applied' ? 'selected' : '' }}>Partially Applied</option>
                        <option value="fully_applied" {{ request('status') == 'fully_applied' ? 'selected' : '' }}>Fully Applied</option>
                    </select>
                </div>
            </div>
            
            <div class="flex space-x-3">
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                    <i class="fas fa-search mr-2"></i>
                    Filter Statements
                </button>
                
                <a href="{{ route('admin.share-bonus.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                    <i class="fas fa-times mr-2"></i>
                    Clear Filters
                </a>
            </div>
        </form>
    </div>
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-600 mt-1"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">Success</h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-600 mt-1"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Error</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Statements Table -->
    @if($statements->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Share Bonus Statements</h3>
                    <p class="text-sm text-gray-500">{{ $statements->total() }} statements found</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statement #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Income</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Share Bonus Pool</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($statements as $statement)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $statement->statement_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $statement->period_description }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $statement->branch ? $statement->branch->name : 'All Branches' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $statement->formatted_net_income }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $statement->formatted_share_bonus_pool }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($statement->status === 'generated')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Generated
                                        </span>
                                    @elseif($statement->status === 'partially_applied')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Partially Applied
                                        </span>
                                    @elseif($statement->status === 'fully_applied')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Fully Applied
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ ucfirst($statement->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-1">
                                        <a href="{{ route('admin.share-bonus.show', $statement->id) }}" 
                                           class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded text-xs"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.share-bonus.print', $statement->id) }}" 
                                           target="_blank"
                                           class="text-purple-600 hover:text-purple-900 px-2 py-1 rounded text-xs"
                                           title="Print Statement">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="{{ route('admin.share-bonus.export-pdf', $statement->id) }}" 
                                           class="text-red-600 hover:text-red-900 px-2 py-1 rounded text-xs"
                                           title="Export PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        <a href="{{ route('admin.share-bonus.export-excel', $statement->id) }}" 
                                           class="text-green-600 hover:text-green-900 px-2 py-1 rounded text-xs"
                                           title="Export Excel">
                                            <i class="fas fa-file-excel"></i>
                                        </a>
                                        @if($statement->status === 'generated')
                                            <form method="POST" action="{{ route('admin.share-bonus.destroy', $statement->id) }}" 
                                                  class="inline" 
                                                  onsubmit="return confirm('Are you sure you want to delete this share bonus statement? This action cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 px-2 py-1 rounded text-xs"
                                                        title="Delete Statement">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($statements->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $statements->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Statements Found</h3>
            <p class="text-gray-500 mb-4">No share bonus statements match your current filters.</p>
            <a href="{{ route('admin.share-bonus.index') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                Clear Filters
            </a>
        </div>
    @endif

    <!-- Introduction Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-percentage text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Share Bonus Distribution</h3>
                <p class="text-gray-600 mb-4">
                    Share bonus is a distribution of profits to members based on their savings account balances. 
                    The system calculates the bonus proportionally according to each member's contribution to the total savings pool.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="bg-gray-50 p-3 rounded">
                        <div class="font-medium text-gray-900">Calculation Method</div>
                        <div class="text-gray-600">Based on savings balance proportion</div>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <div class="font-medium text-gray-900">Distribution</div>
                        <div class="text-gray-600">Automatic to active accounts</div>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <div class="font-medium text-gray-900">Tracking</div>
                        <div class="text-gray-600">Full audit trail maintained</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Generate Share Bonus Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-6">Generate Share Bonus Statement</h3>
        
        <form action="{{ route('admin.share-bonus.generate') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Date Range Options -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Range Options</label>
                <div class="flex flex-wrap gap-2 mb-4">
                    <button type="button" onclick="setGenerateDateRange('today')" 
                            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                        Today
                    </button>
                    <button type="button" onclick="setGenerateDateRange('this_week')" 
                            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                        This Week
                    </button>
                    <button type="button" onclick="setGenerateDateRange('this_month')" 
                            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 bg-blue-100 border-blue-300">
                        This Month
                    </button>
                    <button type="button" onclick="setGenerateDateRange('last_month')" 
                            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                        Last Month
                    </button>
                    <button type="button" onclick="setGenerateDateRange('this_quarter')" 
                            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                        This Quarter
                    </button>
                    <button type="button" onclick="setGenerateDateRange('this_year')" 
                            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                        This Year
                    </button>
                    <button type="button" onclick="setGenerateDateRange('all_time')" 
                            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                        All Time
                    </button>
                    <button type="button" onclick="setGenerateDateRange('custom')" 
                            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                        Custom Range
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Date Range -->
                <div>
                    <label for="generate_start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" 
                           id="generate_start_date" 
                           name="start_date" 
                           value="{{ old('start_date', date('Y-m-01')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="generate_end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" 
                           id="generate_end_date" 
                           name="end_date" 
                           value="{{ old('end_date', date('Y-m-t')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Branch Selection -->
                <div>
                    <label for="branch_id" class="block text-sm font-medium text-gray-700 mb-2">Branch</label>
                    <select id="branch_id" 
                            name="branch_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Share Bonus Percentage -->
                <div>
                    <label for="share_bonus_percentage" class="block text-sm font-medium text-gray-700 mb-2">Share Bonus Percentage (%)</label>
                    <input type="number" 
                           id="share_bonus_percentage" 
                           name="share_bonus_percentage" 
                           value="{{ old('share_bonus_percentage', '10') }}"
                           min="0" 
                           max="100" 
                           step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('share_bonus_percentage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea id="notes" 
                          name="notes" 
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Add any notes about this share bonus calculation...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-calculator mr-2"></i>
                    Generate Share Bonus Statement
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Date range utility functions
function getDateRanges() {
    const today = new Date();
    const year = today.getFullYear();
    const month = today.getMonth();
    const date = today.getDate();
    
    return {
        today: {
            start: new Date(year, month, date).toISOString().split('T')[0],
            end: new Date(year, month, date).toISOString().split('T')[0]
        },
        this_week: {
            start: new Date(year, month, date - today.getDay()).toISOString().split('T')[0],
            end: new Date(year, month, date + (6 - today.getDay())).toISOString().split('T')[0]
        },
        this_month: {
            start: new Date(year, month, 1).toISOString().split('T')[0],
            end: new Date(year, month + 1, 0).toISOString().split('T')[0]
        },
        last_month: {
            start: new Date(year, month - 1, 1).toISOString().split('T')[0],
            end: new Date(year, month, 0).toISOString().split('T')[0]
        },
        this_quarter: {
            start: new Date(year, Math.floor(month / 3) * 3, 1).toISOString().split('T')[0],
            end: new Date(year, Math.floor(month / 3) * 3 + 3, 0).toISOString().split('T')[0]
        },
        this_year: {
            start: new Date(year, 0, 1).toISOString().split('T')[0],
            end: new Date(year, 11, 31).toISOString().split('T')[0]
        },
        all_time: {
            start: '',
            end: ''
        }
    };
}

// Filter date range functions
function setDateRange(range) {
    const ranges = getDateRanges();
    const startDateField = document.getElementById('filter_start_date');
    const endDateField = document.getElementById('filter_end_date');
    
    if (range === 'all') {
        startDateField.value = '';
        endDateField.value = '';
    } else if (range === 'custom') {
        // Don't change the values, let user input custom dates
        return;
    } else if (ranges[range]) {
        startDateField.value = ranges[range].start;
        endDateField.value = ranges[range].end;
    }
    
    // Update button styles
    updateFilterButtonStyles(range === 'all' ? 'all' : (range === 'custom' ? 'custom' : 'preset'));
    
    // Auto-submit the filter form
    setTimeout(applyFilters, 100);
}

// Generate date range functions
function setGenerateDateRange(range) {
    const ranges = getDateRanges();
    const startDateField = document.getElementById('generate_start_date');
    const endDateField = document.getElementById('generate_end_date');
    
    if (range === 'all_time') {
        // For generation, we need actual dates, so use a very wide range
        startDateField.value = '2020-01-01';
        endDateField.value = new Date().toISOString().split('T')[0];
    } else if (range === 'custom') {
        // Don't change the values, let user input custom dates
        return;
    } else if (ranges[range]) {
        startDateField.value = ranges[range].start;
        endDateField.value = ranges[range].end;
    }
    
    // Update button styles
    updateGenerateButtonStyles(range);
}

// Update button styles for filter section
function updateFilterButtonStyles(activeType) {
    const buttons = document.querySelectorAll('[onclick^="setDateRange"]');
    buttons.forEach(button => {
        button.classList.remove('bg-blue-100', 'border-blue-300');
        button.classList.add('border-gray-300');
    });
    
    if (activeType === 'all') {
        const allButton = document.querySelector('[onclick="setDateRange(\'all\')"]');
        if (allButton) {
            allButton.classList.add('bg-blue-100', 'border-blue-300');
            allButton.classList.remove('border-gray-300');
        }
    } else if (activeType === 'custom') {
        const customButton = document.querySelector('[onclick="setDateRange(\'custom\')"]');
        if (customButton) {
            customButton.classList.add('bg-blue-100', 'border-blue-300');
            customButton.classList.remove('border-gray-300');
        }
    }
}

// Update button styles for generate section
function updateGenerateButtonStyles(activeRange) {
    const buttons = document.querySelectorAll('[onclick^="setGenerateDateRange"]');
    buttons.forEach(button => {
        button.classList.remove('bg-blue-100', 'border-blue-300');
        button.classList.add('border-gray-300');
    });
    
    const activeButton = document.querySelector(`[onclick="setGenerateDateRange('${activeRange}')"]`);
    if (activeButton) {
        activeButton.classList.add('bg-blue-100', 'border-blue-300');
        activeButton.classList.remove('border-gray-300');
    }
}

// Filter functionality
function applyFilters() {
    const form = document.querySelector('form[action*="share-bonus.index"]');
    if (form) {
        form.submit();
    }
}

function clearFilters() {
    // Clear all form inputs
    const startDate = document.getElementById('filter_start_date');
    const endDate = document.getElementById('filter_end_date');
    const branchId = document.getElementById('branch_id');
    const status = document.getElementById('status');
    
    if (startDate) startDate.value = '';
    if (endDate) endDate.value = '';
    if (branchId) branchId.value = '';
    if (status) status.value = '';
    
    // Update button styles to show "All Time" as active
    updateFilterButtonStyles('all');
    
    // Submit form to clear filters
    applyFilters();
}

// Initialize the form state on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set initial button styles based on current filter values
    const startDate = document.getElementById('filter_start_date');
    const endDate = document.getElementById('filter_end_date');
    
    if (startDate && endDate) {
        if (!startDate.value && !endDate.value) {
            updateFilterButtonStyles('all');
        } else {
            updateFilterButtonStyles('custom');
        }
    }
    
    // Set initial generate button style (default to "This Month")
    updateGenerateButtonStyles('this_month');
    
    // Auto-submit form when filters change
    const filterForm = document.querySelector('form[action*="share-bonus.index"]');
    if (filterForm) {
        const filterInputs = filterForm.querySelectorAll('select, input[type="date"]');
        filterInputs.forEach(input => {
            input.addEventListener('change', function() {
                // Update button styles when date inputs change manually
                if (input.id === 'filter_start_date' || input.id === 'filter_end_date') {
                    const startVal = document.getElementById('filter_start_date').value;
                    const endVal = document.getElementById('filter_end_date').value;
                    if (!startVal && !endVal) {
                        updateFilterButtonStyles('all');
                    } else {
                        updateFilterButtonStyles('custom');
                    }
                }
                setTimeout(applyFilters, 300); // Small delay to allow for multiple quick changes
            });
        });
    }
    
    // Update generate button styles when date inputs change manually
    const generateStartDate = document.getElementById('generate_start_date');
    const generateEndDate = document.getElementById('generate_end_date');
    
    if (generateStartDate && generateEndDate) {
        [generateStartDate, generateEndDate].forEach(input => {
            input.addEventListener('change', function() {
                updateGenerateButtonStyles('custom');
            });
        });
    }
});
</script>
@endpush