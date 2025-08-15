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
                <p class="font-medium">{{ now()->format('M d, Y H:i') }}</p>
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
                <p class="text-sm text-green-500">Share Bonus (30%)</p>
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
                <p class="text-xs text-purple-500 mt-1">After share bonus & expenses</p>
            </div>
        </div>
    </div>
    
    <!-- Share Bonus Distribution -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Share Bonus Distribution</h3>
        <p class="text-gray-600 mb-4">Share bonuses are distributed proportionally based on members' savings account balances.</p>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Number</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member Name</th>
                        <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Savings Balance</th>
                        <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Proportion</th>
                        <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Bonus Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($data['share_bonus']['member_bonuses'] as $bonus)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $bonus['account_number'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $bonus['member_name'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($bonus['savings_balance'], 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($bonus['proportion'] * 100, 2) }}%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($bonus['bonus_amount'], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" colspan="3"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">100%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">{{ number_format($data['share_bonus']['total_distributed'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
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