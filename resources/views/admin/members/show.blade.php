@extends('layouts.admin')

@section('title', 'Member Details')
@section('subtitle', 'View member profile and financial information')

@section('content')
<div class="space-y-6">
    <!-- Member Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start">
            <div class="flex items-start">
                <div class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center mr-6">
                    <i class="fas fa-user text-gray-600 text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $member->first_name }} {{ $member->last_name }}</h3>
                    <p class="text-gray-600">Member ID: {{ $member->member_id }}</p>
                    <p class="text-gray-700 mt-1">{{ $member->branch->name }}</p>
                    
                    <div class="mt-3 space-y-1">
                        <p class="text-gray-600">
                            <i class="fas fa-phone mr-2"></i> {{ $member->phone }}
                        </p>
                        
                        @if($member->email)
                            <p class="text-gray-600">
                                <i class="fas fa-envelope mr-2"></i> {{ $member->email }}
                            </p>
                        @endif
                        
                        <p class="text-gray-600">
                            <i class="fas fa-map-marker-alt mr-2"></i> {{ $member->address }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="flex space-x-2">
                <a href="{{ route('admin.members.edit', $member) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <a href="{{ route('admin.members.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Member Information Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Personal Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500">Date of Birth</p>
                    <p class="font-medium">{{ $member->date_of_birth->format('M d, Y') }} ({{ $member->date_of_birth->age }} years)</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Gender</p>
                    <p class="font-medium">{{ ucfirst($member->gender) }}</p>
                </div>
                @if($member->occupation)
                    <div>
                        <p class="text-sm text-gray-500">Occupation</p>
                        <p class="font-medium">{{ $member->occupation }}</p>
                    </div>
                @endif
                @if($member->citizenship_number)
                    <div>
                        <p class="text-sm text-gray-500">Citizenship Number</p>
                        <p class="font-medium">{{ $member->citizenship_number }}</p>
                    </div>
                @endif
                <div>
                    <p class="text-sm text-gray-500">Member Since</p>
                    <p class="font-medium">{{ $member->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Family Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Family Information</h3>
            <div class="space-y-3">
                @if($member->father_name)
                    <div>
                        <p class="text-sm text-gray-500">Father's Name</p>
                        <p class="font-medium">{{ $member->father_name }}</p>
                    </div>
                @endif
                @if($member->mother_name)
                    <div>
                        <p class="text-sm text-gray-500">Mother's Name</p>
                        <p class="font-medium">{{ $member->mother_name }}</p>
                    </div>
                @endif
                @if($member->spouse_name)
                    <div>
                        <p class="text-sm text-gray-500">Spouse Name</p>
                        <p class="font-medium">{{ $member->spouse_name }}</p>
                    </div>
                @endif
                @if($member->emergency_contact_name)
                    <div>
                        <p class="text-sm text-gray-500">Emergency Contact</p>
                        <p class="font-medium">{{ $member->emergency_contact_name }}</p>
                        @if($member->emergency_contact_phone)
                            <p class="text-sm text-gray-600">{{ $member->emergency_contact_phone }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('admin.loans.create') }}?member={{ $member->id }}" 
                   class="block w-full bg-green-600 hover:bg-green-700 text-white text-center px-4 py-2 rounded-lg">
                    <i class="fas fa-plus mr-2"></i> New Loan
                </a>
                <a href="{{ route('admin.savings.create') }}?member={{ $member->id }}" 
                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded-lg">
                    <i class="fas fa-piggy-bank mr-2"></i> New Savings Account
                </a>
                <a href="{{ route('admin.transactions.create') }}?member={{ $member->id }}" 
                   class="block w-full bg-purple-600 hover:bg-purple-700 text-white text-center px-4 py-2 rounded-lg">
                    <i class="fas fa-exchange-alt mr-2"></i> New Transaction
                </a>
            </div>
        </div>
    </div>

    <!-- Financial Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-hand-holding-usd text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $member->loans->count() }}</h3>
                    <p class="text-gray-600">Total Loans</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-piggy-bank text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $member->savingsAccounts->count() }}</h3>
                    <p class="text-gray-600">Savings Accounts</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-wallet text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">रू {{ number_format($member->savingsAccounts->sum('balance'), 2) }}</h3>
                    <p class="text-gray-600">Total Savings</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Loans Section -->
    @if($member->loans->count() > 0)
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Loans</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($member->loans as $loan)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $loan->loanType->name }}</h4>
                                    <p class="text-sm text-gray-600">Amount: रू {{ number_format($loan->amount, 2) }}</p>
                                    <p class="text-sm text-gray-600">Interest: {{ $loan->interest_rate }}% | Term: {{ $loan->term_months }} months</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    @switch($loan->status)
                                        @case('pending') bg-yellow-100 text-yellow-800 @break
                                        @case('approved') bg-blue-100 text-blue-800 @break
                                        @case('active') bg-green-100 text-green-800 @break
                                        @case('closed') bg-gray-100 text-gray-800 @break
                                        @case('defaulted') bg-red-100 text-red-800 @break
                                    @endswitch">
                                    {{ ucfirst($loan->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Savings Accounts Section -->
    @if($member->savingsAccounts->count() > 0)
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Savings Accounts</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($member->savingsAccounts as $account)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $account->savingsType->name }}</h4>
                                    <p class="text-sm text-gray-600">Account: {{ $account->account_number }}</p>
                                    <p class="text-sm text-gray-600">Interest Rate: {{ $account->interest_rate }}%</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-semibold text-gray-900">रू {{ number_format($account->balance, 2) }}</p>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        @switch($account->status)
                                            @case('active') bg-green-100 text-green-800 @break
                                            @case('inactive') bg-yellow-100 text-yellow-800 @break
                                            @case('closed') bg-red-100 text-red-800 @break
                                        @endswitch">
                                        {{ ucfirst($account->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection