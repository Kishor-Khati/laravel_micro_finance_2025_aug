@extends('layouts.admin')

@section('title', 'Loan Management')
@section('subtitle', 'Manage microfinance loans')

@section('content')
<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center">
        <h3 class="text-xl font-semibold text-gray-900">All Loans</h3>
        <a href="{{ route('admin.loans.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            New Loan
        </a>
    </div>

    <!-- Loans Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loan Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($loans as $loan)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $loan->member->first_name }} {{ $loan->member->last_name }}</div>
                        <div class="text-sm text-gray-500">ID: {{ $loan->member->member_id }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $loan->loanType->name }}</div>
                        <div class="text-sm text-gray-500">{{ $loan->interest_rate }}% - {{ $loan->term_months }} months</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">रू {{ number_format($loan->amount, 2) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @switch($loan->status)
                                @case('pending') bg-yellow-100 text-yellow-800 @break
                                @case('approved') bg-blue-100 text-blue-800 @break
                                @case('active') bg-green-100 text-green-800 @break
                                @case('closed') bg-gray-100 text-gray-800 @break
                                @case('defaulted') bg-red-100 text-red-800 @break
                                @default bg-gray-100 text-gray-800
                            @endswitch">
                            {{ ucfirst($loan->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.loans.show', $loan) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                        <a href="{{ route('admin.loans.edit', $loan) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        <form method="POST" action="{{ route('admin.loans.destroy', $loan) }}" class="inline">
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
        {{ $loans->links() }}
    </div>
</div>
@endsection