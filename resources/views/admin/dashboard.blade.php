@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', 'Overview of your microfinance operations')

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $stats['total_users'] }}</h3>
                    <p class="text-gray-600">Total Users</p>
                </div>
            </div>
        </div>

        <!-- Total Members -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-user-friends text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $stats['total_members'] }}</h3>
                    <p class="text-gray-600">Total Members</p>
                </div>
            </div>
        </div>

        <!-- Active Loans -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-hand-holding-usd text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $stats['active_loans'] }}</h3>
                    <p class="text-gray-600">Active Loans</p>
                </div>
            </div>
        </div>

        <!-- Total Savings -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-piggy-bank text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">रू {{ number_format($stats['total_savings'], 2) }}</h3>
                    <p class="text-gray-600">Total Savings</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Financial Summary</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Raw Income -->
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-blue-700">Raw Income</p>
                        <p class="text-xl font-bold text-blue-900">रू {{ number_format($stats['total_raw_income'] ?? 0, 2) }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-hand-holding-usd text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-blue-700 mt-2">Total loan interest earned</p>
            </div>
            
            <!-- Share Bonuses -->
            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green-700">Share Bonuses</p>
                        <p class="text-xl font-bold text-green-900">रू {{ number_format($stats['total_share_bonus'] ?? 0, 2) }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-green-700 mt-2">Distributed to {{ $stats['members_with_savings']->count() }} members</p>
            </div>
            
            <!-- Expenses -->
            <div class="bg-red-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-red-700">Expenses</p>
                        <p class="text-xl font-bold text-red-900">रू {{ number_format($stats['total_expenses'] ?? 0, 2) }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-file-invoice-dollar text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-red-700 mt-2">Total approved expenses</p>
            </div>
            
            <!-- Final Balance -->
            <div class="bg-purple-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-purple-700">Net Income</p>
                        <p class="text-xl font-bold text-purple-900">रू {{ number_format($stats['final_balance'] ?? 0, 2) }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-purple-700 mt-2">Raw Income - Share Bonuses - Expenses</p>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Overview -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Overview</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">New Loans This Month</span>
                    <span class="font-semibold">{{ $stats['monthly_loans'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">New Savings This Month</span>
                    <span class="font-semibold">रू {{ number_format($stats['monthly_savings'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Branches</span>
                    <span class="font-semibold">{{ $stats['total_branches'] }}</span>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Transactions</h3>
            @if($stats['recent_transactions']->count() > 0)
                <div class="space-y-3">
                    @foreach($stats['recent_transactions'] as $transaction)
                        <div class="flex justify-between items-center py-2 border-b border-gray-200 last:border-b-0">
                            <div>
                                <p class="font-medium text-gray-900">{{ ucfirst($transaction->type) }}</p>
                                <p class="text-sm text-gray-600">{{ $transaction->savingsAccount->member->first_name ?? 'N/A' }} {{ $transaction->savingsAccount->member->last_name ?? '' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold {{ $transaction->type === 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->type === 'deposit' ? '+' : '-' }}रू {{ number_format($transaction->amount, 2) }}
                                </p>
                                <p class="text-sm text-gray-500">{{ $transaction->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No recent transactions</p>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.members.create') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <i class="fas fa-user-plus text-blue-600 text-xl mr-3"></i>
                <span class="font-medium text-blue-900">Add Member</span>
            </a>
            
            <a href="{{ route('admin.loans.create') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                <i class="fas fa-plus-circle text-green-600 text-xl mr-3"></i>
                <span class="font-medium text-green-900">New Loan</span>
            </a>
            
            <a href="{{ route('admin.savings.create') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                <i class="fas fa-piggy-bank text-purple-600 text-xl mr-3"></i>
                <span class="font-medium text-purple-900">New Savings</span>
            </a>
            
            <a href="{{ route('admin.transactions.create') }}" class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                <i class="fas fa-exchange-alt text-yellow-600 text-xl mr-3"></i>
                <span class="font-medium text-yellow-900">New Transaction</span>
            </a>
        </div>
    </div>
</div>
@endsection