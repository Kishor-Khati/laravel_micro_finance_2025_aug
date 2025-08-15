@extends('layouts.admin')

@section('title', 'Finance Statement')
@section('subtitle', 'Financial statement with share bonus calculations')

@section('content')
<div class="space-y-6">
    <!-- Statement Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Finance Statement</h3>
            <div class="flex space-x-2">
                <a href="{{ route('admin.finance-statements.export-pdf', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d'), 'branch_id' => request('branch_id')]) }}" 
                   class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </a>
                <a href="{{ route('admin.finance-statements.export-excel', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d'), 'branch_id' => request('branch_id')]) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                </a>
                <a href="{{ route('admin.finance-statements.index') }}" 
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
                <p class="font-medium">{{ now()->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>
    
    <!-- Financial Summary -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Financial Summary</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <!-- Raw Income -->
            <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                <p class="text-sm text-gray-500 mb-1">Raw Income</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($data['total_raw_income'], 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">Total loan interest earned</p>
            </div>
            
            <!-- Share Bonuses -->
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                <p class="text-sm text-gray-500 mb-1">Share Bonuses</p>
                <p class="text-2xl font-bold text-blue-600">{{ number_format($data['share_bonus']['total_share_bonus'], 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">Distributed to {{ $data['share_bonus']['members_with_savings']->count() }} members</p>
            </div>
            
            <!-- Expenses -->
            <div class="bg-red-50 p-4 rounded-lg border border-red-100">
                <p class="text-sm text-gray-500 mb-1">Expenses</p>
                <p class="text-2xl font-bold text-red-600">{{ number_format($data['total_expenses'], 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">Total operational expenses</p>
            </div>
            
            <!-- Net Income -->
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
                <p class="text-sm text-gray-500 mb-1">Net Income</p>
                <p class="text-2xl font-bold text-purple-600">{{ number_format($data['final_balance'], 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">Raw Income - Share Bonuses - Expenses</p>
            </div>
        </div>
        
        <!-- Financial Breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Loans Summary -->
            <div>
                <h4 class="text-md font-medium text-gray-800 mb-3">Loans</h4>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Loans:</span>
                        <span class="font-medium">{{ $data['loans']['total'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Active Loans:</span>
                        <span class="font-medium">{{ $data['loans']['active'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Amount:</span>
                        <span class="font-medium">{{ number_format($data['loans']['amount'], 2) }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Savings Summary -->
            <div>
                <h4 class="text-md font-medium text-gray-800 mb-3">Savings</h4>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Accounts:</span>
                        <span class="font-medium">{{ $data['savings']['total'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Active Accounts:</span>
                        <span class="font-medium">{{ $data['savings']['active'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Balance:</span>
                        <span class="font-medium">{{ number_format($data['savings']['balance'], 2) }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Transactions Summary -->
            <div>
                <h4 class="text-md font-medium text-gray-800 mb-3">Transactions</h4>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Transactions:</span>
                        <span class="font-medium">{{ $data['transactions']['total'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Deposits:</span>
                        <span class="font-medium">{{ number_format($data['transactions']['deposits'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Withdrawals:</span>
                        <span class="font-medium">{{ number_format($data['transactions']['withdrawals'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Share Bonus Details -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Share Bonus Distribution</h3>
        
        @if(count($data['share_bonus']['bonus_details']) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Savings Balance</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proportion (%)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bonus Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($data['share_bonus']['bonus_details'] as $detail)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $detail['account_number'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $detail['member_name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($detail['savings_balance'], 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($detail['proportion'] * 100, 2) }}%</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">{{ number_format($detail['bonus_amount'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-900">Total Share Bonus:</td>
                            <td class="px-6 py-3 text-sm font-bold text-blue-600">{{ number_format($data['share_bonus']['total_share_bonus'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="bg-yellow-50 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-yellow-600 mt-1"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">No Share Bonuses</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>There are no active savings accounts or the total savings balance is zero for the selected period.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection