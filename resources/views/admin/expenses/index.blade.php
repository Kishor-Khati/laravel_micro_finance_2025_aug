@extends('layouts.admin')

@section('title', 'Expense Management')
@section('subtitle', 'Track and manage operational expenses')

@section('content')
<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">All Expenses</h2>
            <p class="text-gray-600">Manage and track all operational expenses</p>
        </div>
        <a href="{{ route('admin.expenses.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i> Add Expense
        </a>
    </div>

    <!-- Expense Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">रू {{ number_format($expenses->sum('amount'), 2) }}</h3>
                    <p class="text-gray-600">Total Expenses</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                    <i class="fas fa-calendar-day text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">रू {{ number_format(\App\Models\Expense::whereMonth('expense_date', date('m'))->sum('amount'), 2) }}</h3>
                    <p class="text-gray-600">This Month</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-calendar-week text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">रू {{ number_format(\App\Models\Expense::whereBetween('expense_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount'), 2) }}</h3>
                    <p class="text-gray-600">This Week</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-receipt text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $expenses->count() }}</h3>
                    <p class="text-gray-600">Total Records</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                <select name="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Categories</option>
                    <option value="office_supplies">Office Supplies</option>
                    <option value="utilities">Utilities</option>
                    <option value="rent">Rent</option>
                    <option value="marketing">Marketing</option>
                    <option value="travel">Travel</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div>
                <label for="branch" class="block text-sm font-medium text-gray-700">Branch</label>
                <select name="branch" id="branch" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Branches</option>
                    @foreach(\App\Models\Branch::all() as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700">Date From</label>
                <input type="date" name="date_from" id="date_from" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Expenses Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Expense Records</h3>
        </div>
        
        @if($expenses->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($expenses as $expense)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $expense->expense_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ ucwords(str_replace('_', ' ', $expense->category)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="max-w-xs truncate">{{ $expense->description }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">
                                रू {{ number_format($expense->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $expense->branch->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    @switch($expense->payment_method)
                                        @case('cash') bg-green-100 text-green-800 @break
                                        @case('bank_transfer') bg-blue-100 text-blue-800 @break
                                        @case('cheque') bg-yellow-100 text-yellow-800 @break
                                        @case('online') bg-purple-100 text-purple-800 @break
                                    @endswitch">
                                    {{ ucwords(str_replace('_', ' ', $expense->payment_method)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.expenses.show', $expense) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.expenses.edit', $expense) }}" class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.expenses.destroy', $expense) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this expense?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $expenses->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <i class="fas fa-receipt text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No expenses found</h3>
                <p class="text-gray-600 mb-4">Start by adding your first expense record.</p>
                <a href="{{ route('admin.expenses.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-plus mr-2"></i> Add First Expense
                </a>
            </div>
        @endif
    </div>
</div>
@endsection