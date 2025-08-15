@extends('layouts.admin')

@section('title', 'Savings Account Details')
@section('subtitle', 'View account information and transaction history')

@section('content')
<div class="space-y-6">
    <!-- Account Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $savingsAccount->savingsType->name }}</h3>
                <p class="text-gray-600">Account: {{ $savingsAccount->account_number }}</p>
                <p class="text-gray-700 mt-1">{{ $savingsAccount->member->first_name }} {{ $savingsAccount->member->last_name }} ({{ $savingsAccount->member->member_id }})</p>
                
                <div class="mt-4">
                    <span class="px-3 py-1 text-sm font-semibold rounded-full 
                        @switch($savingsAccount->status)
                            @case('active') bg-green-100 text-green-800 @break
                            @case('inactive') bg-yellow-100 text-yellow-800 @break
                            @case('closed') bg-red-100 text-red-800 @break
                        @endswitch">
                        {{ ucfirst($savingsAccount->status) }}
                    </span>
                </div>
            </div>
            
            <div class="flex space-x-2">
                <a href="{{ route('admin.savings.edit', $savingsAccount) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <a href="{{ route('admin.savings.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Account Information Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Account Balance -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Current Balance</h3>
            <div class="text-center">
                <p class="text-4xl font-bold text-green-600 mb-2">रू {{ number_format($savingsAccount->balance, 2) }}</p>
                <p class="text-sm text-gray-500">Available Balance</p>
            </div>
            
            <div class="mt-6 space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Interest Rate:</span>
                    <span class="text-sm font-medium">{{ $savingsAccount->interest_rate }}% p.a.</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Account Type:</span>
                    <span class="text-sm font-medium">{{ $savingsAccount->savingsType->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Opened:</span>
                    <span class="text-sm font-medium">{{ $savingsAccount->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Member Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Account Holder</h3>
            <div class="flex items-center mb-4">
                <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center mr-4">
                    <i class="fas fa-user text-gray-600 text-xl"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">{{ $savingsAccount->member->first_name }} {{ $savingsAccount->member->last_name }}</p>
                    <p class="text-sm text-gray-600">{{ $savingsAccount->member->member_id }}</p>
                </div>
            </div>
            
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Phone:</span>
                    <span class="text-sm font-medium">{{ $savingsAccount->member->phone }}</span>
                </div>
                
                @if($savingsAccount->member->email)
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Email:</span>
                    <span class="text-sm font-medium">{{ $savingsAccount->member->email }}</span>
                </div>
                @endif
                
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Branch:</span>
                    <span class="text-sm font-medium">{{ $savingsAccount->member->branch->name }}</span>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t">
                <a href="{{ route('admin.members.show', $savingsAccount->member) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View Member Profile →
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
            
            <!-- Deposit Form -->
            <form method="POST" action="{{ route('admin.savings.deposit', $savingsAccount) }}" class="mb-4">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label for="deposit_amount" class="block text-sm font-medium text-gray-700">Deposit Amount</label>
                        <input type="number" name="amount" id="deposit_amount" step="0.01" min="0.01" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div>
                        <label for="deposit_description" class="block text-sm font-medium text-gray-700">Description</label>
                        <input type="text" name="description" id="deposit_description" placeholder="Optional"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-plus mr-2"></i> Deposit
                    </button>
                </div>
            </form>

            <!-- Withdraw Form -->
            <form method="POST" action="{{ route('admin.savings.withdraw', $savingsAccount) }}">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label for="withdraw_amount" class="block text-sm font-medium text-gray-700">Withdraw Amount</label>
                        <input type="number" name="amount" id="withdraw_amount" step="0.01" min="0.01" max="{{ $savingsAccount->balance }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        <p class="text-xs text-gray-500 mt-1">Max: रू {{ number_format($savingsAccount->balance, 2) }}</p>
                    </div>
                    <div>
                        <label for="withdraw_description" class="block text-sm font-medium text-gray-700">Description</label>
                        <input type="text" name="description" id="withdraw_description" placeholder="Optional"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    </div>
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-minus mr-2"></i> Withdraw
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Account Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-arrow-up text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $savingsAccount->transactions->where('transaction_type', 'deposit')->count() }}</h3>
                    <p class="text-gray-600">Total Deposits</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-arrow-down text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $savingsAccount->transactions->where('transaction_type', 'withdrawal')->count() }}</h3>
                    <p class="text-gray-600">Total Withdrawals</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-plus text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">रू {{ number_format($savingsAccount->transactions->where('transaction_type', 'deposit')->sum('amount'), 2) }}</h3>
                    <p class="text-gray-600">Total Deposited</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-minus text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">रू {{ number_format($savingsAccount->transactions->where('transaction_type', 'withdrawal')->sum('amount'), 2) }}</h3>
                    <p class="text-gray-600">Total Withdrawn</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    @if($savingsAccount->transactions->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Transaction History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance After</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($savingsAccount->transactions->sortByDesc('created_at')->take(10) as $transaction)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transaction->transaction_date->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($transaction->transaction_type === 'deposit') bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($transaction->transaction_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $transaction->transaction_type === 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->transaction_type === 'deposit' ? '+' : '-' }}रू {{ number_format($transaction->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transaction->description ?: 'No description' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            रू {{ number_format($transaction->balance_after ?? 0, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($savingsAccount->transactions->count() > 10)
        <div class="px-6 py-4 border-t border-gray-200">
            <a href="{{ route('admin.transactions.index') }}?account={{ $savingsAccount->id }}" class="text-blue-600 hover:text-blue-800 text-sm">
                View all {{ $savingsAccount->transactions->count() }} transactions →
            </a>
        </div>
        @endif
    </div>
    @else
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <div class="py-8">
            <i class="fas fa-receipt text-gray-400 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No transactions yet</h3>
            <p class="text-gray-600">Start by making a deposit to this account.</p>
        </div>
    </div>
    @endif
</div>
@endsection