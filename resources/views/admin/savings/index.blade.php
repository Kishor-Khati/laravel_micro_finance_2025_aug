@extends('layouts.admin')

@section('title', 'Savings Management')
@section('subtitle', 'Manage member savings accounts')

@section('content')
<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center">
        <h3 class="text-xl font-semibold text-gray-900">All Savings Accounts</h3>
        <a href="{{ route('admin.savings.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            New Account
        </a>
    </div>

    <!-- Savings Accounts Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($savingsAccounts as $account)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $account->account_number }}</div>
                        <div class="text-sm text-gray-500">{{ $account->interest_rate }}% interest</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $account->member->first_name }} {{ $account->member->last_name }}</div>
                        <div class="text-sm text-gray-500">ID: {{ $account->member->member_id }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $account->savingsType->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">रू {{ number_format($account->balance, 2) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @switch($account->status)
                                @case('active') bg-green-100 text-green-800 @break
                                @case('inactive') bg-yellow-100 text-yellow-800 @break
                                @case('closed') bg-red-100 text-red-800 @break
                                @default bg-gray-100 text-gray-800
                            @endswitch">
                            {{ ucfirst($account->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.savings.show', $account) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                        <a href="{{ route('admin.savings.edit', $account) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        <form method="POST" action="{{ route('admin.savings.destroy', $account) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $savingsAccounts->links() }}
    </div>
</div>
@endsection