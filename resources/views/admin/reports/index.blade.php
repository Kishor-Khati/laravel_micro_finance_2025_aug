@extends('layouts.admin')

@section('title', 'Reports & Analytics')
@section('subtitle', 'Comprehensive business intelligence and analytics')

@section('content')
<div class="space-y-6">
    <!-- Report Categories -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Financial Reports -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Financial Reports</h3>
                    <p class="text-gray-600">Revenue, expenses, and profit analysis</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="{{ route('admin.reports.financial') }}" class="block text-blue-600 hover:text-blue-800 text-sm">
                    → Comprehensive Financial Overview
                </a>
                <a href="{{ route('admin.reports.financial') }}#trends" class="block text-blue-600 hover:text-blue-800 text-sm">
                    → Monthly Financial Trends
                </a>
                <a href="{{ route('admin.reports.financial') }}#cashflow" class="block text-blue-600 hover:text-blue-800 text-sm">
                    → Cash Flow Analysis
                </a>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.reports.financial') }}" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-block text-center">
                    View Financial Reports
                </a>
            </div>
        </div>

        <!-- Member Reports -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Member Analytics</h3>
                    <p class="text-gray-600">Member demographics and activity</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="{{ route('admin.reports.members') }}" class="block text-blue-600 hover:text-blue-800 text-sm">
                    → Member Demographics
                </a>
                <a href="{{ route('admin.reports.members') }}#growth" class="block text-blue-600 hover:text-blue-800 text-sm">
                    → Membership Growth Trends
                </a>
                <a href="{{ route('admin.reports.members') }}#activity" class="block text-blue-600 hover:text-blue-800 text-sm">
                    → Member Activity Analysis
                </a>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.reports.members') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-block text-center">
                    View Member Reports
                </a>
            </div>
        </div>

        <!-- Loan Reports -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <i class="fas fa-hand-holding-usd text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Loan Portfolio</h3>
                    <p class="text-gray-600">Loan performance and analysis</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="{{ route('admin.reports.loans') }}" class="block text-blue-600 hover:text-blue-800 text-sm">
                    → Portfolio Overview
                </a>
                <a href="{{ route('admin.reports.loans') }}#performance" class="block text-blue-600 hover:text-blue-800 text-sm">
                    → Loan Performance Metrics
                </a>
                <a href="{{ route('admin.reports.loans') }}#default" class="block text-blue-600 hover:text-blue-800 text-sm">
                    → Default Risk Analysis
                </a>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.reports.loans') }}" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg inline-block text-center">
                    View Loan Reports
                </a>
            </div>
        </div>

        <!-- Branch Performance -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-building text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Branch Performance</h3>
                    <p class="text-gray-600">Branch-wise analytics and comparison</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="{{ route('admin.reports.branches') }}" class="block text-blue-600 hover:text-blue-800 text-sm">
                    → Branch Comparison
                </a>
                <a href="{{ route('admin.reports.branches') }}#performance" class="block text-blue-600 hover:text-blue-800 text-sm">
                    → Performance Metrics
                </a>
                <a href="{{ route('admin.reports.branches') }}#staff" class="block text-blue-600 hover:text-blue-800 text-sm">
                    → Staff Productivity
                </a>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.reports.branches') }}" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg inline-block text-center">
                    View Branch Reports
                </a>
            </div>
        </div>

        <!-- Transaction Reports -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4">
                    <i class="fas fa-exchange-alt text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Transaction Analysis</h3>
                    <p class="text-gray-600">Transaction patterns and trends</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="{{ route('admin.reports.transactions') }}" class="block text-blue-600 hover:text-blue-800 text-sm">
                    → Transaction Overview
                </a>
                <a href="{{ route('admin.reports.transactions') }}#patterns" class="block text-blue-600 hover:text-blue-800 text-sm">
                    → Transaction Patterns
                </a>
                <a href="{{ route('admin.reports.transactions') }}#volume" class="block text-blue-600 hover:text-blue-800 text-sm">
                    → Volume Analysis
                </a>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.reports.transactions') }}" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg inline-block text-center">
                    View Transaction Reports
                </a>
            </div>
        </div>

        <!-- Executive Summary -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                    <i class="fas fa-chart-pie text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Executive Summary</h3>
                    <p class="text-gray-600">High-level business overview</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="{{ route('admin.reports.summary') }}" class="block text-blue-600 hover:text-blue-800 text-sm">
                    → Business Overview
                </a>
                <a href="{{ route('admin.reports.summary') }}#kpis" class="block text-blue-600 hover:text-blue-800 text-sm">
                    → Key Performance Indicators
                </a>
                <a href="{{ route('admin.reports.summary') }}#growth" class="block text-blue-600 hover:text-blue-800 text-sm">
                    → Growth Metrics
                </a>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.reports.summary') }}" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg inline-block text-center">
                    View Executive Summary
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Dashboard -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-6">Quick Overview</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600">{{ \App\Models\Member::count() }}</div>
                <div class="text-gray-600">Total Members</div>
                <div class="text-sm text-green-600">{{ \App\Models\Member::whereMonth('created_at', date('m'))->count() }} this month</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600">रू {{ number_format(\App\Models\Loan::sum('amount'), 0) }}</div>
                <div class="text-gray-600">Total Loans</div>
                <div class="text-sm text-green-600">{{ \App\Models\Loan::where('status', 'active')->count() }} active</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600">रू {{ number_format(\App\Models\SavingsAccount::sum('balance'), 0) }}</div>
                <div class="text-gray-600">Total Savings</div>
                <div class="text-sm text-green-600">{{ \App\Models\SavingsAccount::where('status', 'active')->count() }} accounts</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-orange-600">{{ \App\Models\Transaction::count() }}</div>
                <div class="text-gray-600">Total Transactions</div>
                <div class="text-sm text-green-600">{{ \App\Models\Transaction::whereDate('created_at', today())->count() }} today</div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Reporting Activity</h3>
        <div class="space-y-3">
            <div class="flex items-center justify-between py-2 border-b border-gray-200">
                <div class="flex items-center">
                    <i class="fas fa-chart-line text-green-600 mr-3"></i>
                    <span class="text-gray-900">Financial Report Generated</span>
                </div>
                <span class="text-sm text-gray-500">{{ now()->format('M d, Y g:i A') }}</span>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-gray-200">
                <div class="flex items-center">
                    <i class="fas fa-users text-blue-600 mr-3"></i>
                    <span class="text-gray-900">Member Analytics Updated</span>
                </div>
                <span class="text-sm text-gray-500">{{ now()->subHours(2)->format('M d, Y g:i A') }}</span>
            </div>
            <div class="flex items-center justify-between py-2">
                <div class="flex items-center">
                    <i class="fas fa-building text-purple-600 mr-3"></i>
                    <span class="text-gray-900">Branch Performance Report</span>
                </div>
                <span class="text-sm text-gray-500">{{ now()->subHours(4)->format('M d, Y g:i A') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection