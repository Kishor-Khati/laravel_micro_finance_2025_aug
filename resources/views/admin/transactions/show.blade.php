@extends('layouts.admin')

@section('title', 'Transaction Details')
@section('subtitle', 'View transaction information')

@section('content')
<div class="space-y-6">
    <!-- Transaction Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">Transaction #{{ $transaction->id }}</h3>
                <p class="text-gray-600">{{ $transaction->transaction_date->format('M d, Y') }} at {{ $transaction->transaction_date->format('g:i A') }}</p>
                
                <div class="mt-4">
                    <span class="px-3 py-1 text-lg font-semibold rounded-full 
                        @if($transaction->type === 'deposit') bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                        {{ $transaction->type === 'deposit' ? '+' : '-' }}रू {{ number_format($transaction->amount, 2) }}
                    </span>
                    <span class="ml-3 px-2 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-800">
                        {{ ucfirst($transaction->type) }}
                    </span>
                </div>
            </div>
            
            <div class="flex space-x-2">
                <a href="{{ route('admin.transactions.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Transaction Information Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Transaction Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Transaction Details</h3>
            <div class="space-y-4">
                <div class="border-b border-gray-200 pb-3">
                    <p class="text-sm text-gray-500">Transaction ID</p>
                    <p class="text-lg font-semibold text-gray-900">#{{ $transaction->id }}</p>
                </div>
                
                <div class="border-b border-gray-200 pb-3">
                    <p class="text-sm text-gray-500">Amount</p>
                    <p class="text-xl font-bold {{ $transaction->type === 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $transaction->type === 'deposit' ? '+' : '-' }}रू {{ number_format($transaction->amount, 2) }}
                    </p>
                </div>
                
                <div class="border-b border-gray-200 pb-3">
                    <p class="text-sm text-gray-500">Transaction Type</p>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                        @if($transaction->type === 'deposit') bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($transaction->type) }}
                    </span>
                </div>
                
                <div class="border-b border-gray-200 pb-3">
                    <p class="text-sm text-gray-500">Date & Time</p>
                    <p class="font-medium text-gray-900">{{ $transaction->transaction_date->format('M d, Y') }}</p>
                    <p class="text-sm text-gray-600">{{ $transaction->transaction_date->format('g:i A') }}</p>
                </div>
                
                @if($transaction->description)
                <div class="border-b border-gray-200 pb-3">
                    <p class="text-sm text-gray-500">Description</p>
                    <p class="font-medium text-gray-900">{{ $transaction->description }}</p>
                </div>
                @endif
                
                <div>
                    <p class="text-sm text-gray-500">Processed</p>
                    <p class="font-medium text-gray-900">{{ $transaction->created_at->format('M d, Y') }} at {{ $transaction->created_at->format('g:i A') }}</p>
                    <p class="text-sm text-gray-600">{{ $transaction->created_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Account Information</h3>
            
            <!-- Account Details -->
            @if($transaction->savingsAccount)
            <div class="mb-6">
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i class="fas fa-piggy-bank text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $transaction->savingsAccount->savingsType->name ?? 'N/A' }}</h4>
                            <p class="text-sm text-gray-600">{{ $transaction->savingsAccount->account_number }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Current Balance:</span>
                            <span class="text-sm font-medium text-green-600">रू {{ number_format($transaction->savingsAccount->balance, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Interest Rate:</span>
                            <span class="text-sm font-medium">{{ $transaction->savingsAccount->interest_rate }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Account Status:</span>
                            <span class="text-sm font-medium capitalize">{{ $transaction->savingsAccount->status }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Member Details -->
            <div>
                <h4 class="font-medium text-gray-900 mb-3">{{ $transaction->savingsAccount ? 'Account Holder' : 'Member' }}</h4>
                <div class="border border-gray-200 rounded-lg p-4">
                    @if($transaction->savingsAccount)
                    <div class="flex items-center mb-3">
                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                            <i class="fas fa-user text-gray-600"></i>
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-900">{{ $transaction->savingsAccount->member->first_name }} {{ $transaction->savingsAccount->member->last_name }}</h5>
                            <p class="text-sm text-gray-600">{{ $transaction->savingsAccount->member->member_id }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Phone:</span>
                            <span class="text-sm font-medium">{{ $transaction->savingsAccount->member->phone }}</span>
                        </div>
                        
                        @if($transaction->savingsAccount->member->email)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Email:</span>
                            <span class="text-sm font-medium">{{ $transaction->savingsAccount->member->email }}</span>
                        </div>
                        @endif
                        
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Branch:</span>
                            <span class="text-sm font-medium">{{ $transaction->savingsAccount->member->branch->name }}</span>
                        </div>
                    </div>
                    @else
                    <div class="flex items-center mb-3">
                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                            <i class="fas fa-user text-gray-600"></i>
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-900">{{ $transaction->member->first_name }} {{ $transaction->member->last_name }}</h5>
                            <p class="text-sm text-gray-600">{{ $transaction->member->member_id }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Phone:</span>
                            <span class="text-sm font-medium">{{ $transaction->member->phone }}</span>
                        </div>
                        
                        @if($transaction->member->email)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Email:</span>
                            <span class="text-sm font-medium">{{ $transaction->member->email }}</span>
                        </div>
                        @endif
                        
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Branch:</span>
                            <span class="text-sm font-medium">{{ $transaction->member->branch->name }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Related Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if($transaction->savingsAccount)
            <a href="{{ route('admin.savings.show', $transaction->savingsAccount) }}" 
               class="flex items-center justify-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-eye text-blue-600 mr-2"></i>
                <span class="font-medium text-gray-900">View Account</span>
            </a>
            
            <a href="{{ route('admin.members.show', $transaction->savingsAccount->member) }}" 
               class="flex items-center justify-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-user text-green-600 mr-2"></i>
                <span class="font-medium text-gray-900">View Member</span>
            </a>
            
            <a href="{{ route('admin.transactions.create') }}?account={{ $transaction->savingsAccount->id }}" 
               class="flex items-center justify-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-plus text-purple-600 mr-2"></i>
                <span class="font-medium text-gray-900">New Transaction</span>
            </a>
            @else
            <a href="{{ route('admin.members.show', $transaction->member) }}" 
               class="flex items-center justify-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-user text-green-600 mr-2"></i>
                <span class="font-medium text-gray-900">View Member</span>
            </a>
            
            <a href="{{ route('admin.transactions.create') }}" 
               class="flex items-center justify-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-plus text-purple-600 mr-2"></i>
                <span class="font-medium text-gray-900">New Transaction</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Recent Account Transactions -->
    @if($transaction->savingsAccount)
    @php
        $recentTransactions = $transaction->savingsAccount->transactions()
            ->where('id', '!=', $transaction->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    @endphp
    
    @if($recentTransactions->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Account Transactions</h3>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @foreach($recentTransactions as $recentTransaction)
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 last:border-b-0">
                        <div class="flex items-center">
                            <div class="h-8 w-8 rounded-full {{ $recentTransaction->type === 'deposit' ? 'bg-green-100' : 'bg-red-100' }} flex items-center justify-center mr-3">
                                <i class="fas {{ $recentTransaction->type === 'deposit' ? 'fa-arrow-up text-green-600' : 'fa-arrow-down text-red-600' }} text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ ucfirst($recentTransaction->type) }}</p>
                                <p class="text-xs text-gray-500">{{ $recentTransaction->transaction_date->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium {{ $recentTransaction->type === 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $recentTransaction->type === 'deposit' ? '+' : '-' }}रू {{ number_format($recentTransaction->amount, 2) }}
                            </p>
                            <a href="{{ route('admin.transactions.show', $recentTransaction) }}" class="text-xs text-blue-600 hover:text-blue-800">
                                View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    @endif
</div>
@endsection