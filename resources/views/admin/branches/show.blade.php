@extends('layouts.admin')

@section('title', 'Branch Details')
@section('subtitle', 'View branch information and statistics')

@section('content')
<div class="space-y-6">
    <!-- Branch Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $branch->name }}</h3>
                <p class="text-gray-600">Code: {{ $branch->code }}</p>
                <p class="text-gray-700 mt-2">{{ $branch->address }}</p>
                
                @if($branch->phone)
                    <p class="text-gray-600 mt-2">
                        <i class="fas fa-phone mr-2"></i> {{ $branch->phone }}
                    </p>
                @endif
                
                @if($branch->email)
                    <p class="text-gray-600">
                        <i class="fas fa-envelope mr-2"></i> {{ $branch->email }}
                    </p>
                @endif

                @if($branch->manager_name)
                    <p class="text-gray-600 mt-2">
                        <i class="fas fa-user-tie mr-2"></i> Manager: {{ $branch->manager_name }}
                    </p>
                @endif
            </div>
            
            <div class="flex space-x-2">
                <a href="{{ route('admin.branches.edit', $branch) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <a href="{{ route('admin.branches.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $branch->users->count() }}</h3>
                    <p class="text-gray-600">Staff Members</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-user-friends text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $branch->members->count() }}</h3>
                    <p class="text-gray-600">Active Members</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-hand-holding-usd text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $branch->members->flatMap->loans->where('status', 'active')->count() }}</h3>
                    <p class="text-gray-600">Active Loans</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-piggy-bank text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">रू {{ number_format($branch->members->flatMap->savingsAccounts->sum('balance'), 2) }}</h3>
                    <p class="text-gray-600">Total Savings</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Members -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Staff Members</h3>
        </div>
        <div class="p-6">
            @if($branch->users->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($branch->users as $user)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ ucwords(str_replace('_', ' ', $user->role)) }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No staff members assigned to this branch.</p>
            @endif
        </div>
    </div>

    <!-- Recent Members -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Members</h3>
        </div>
        <div class="p-6">
            @if($branch->members->count() > 0)
                <div class="space-y-4">
                    @foreach($branch->members->take(5) as $member)
                        <div class="flex items-center justify-between py-2 border-b border-gray-200 last:border-b-0">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600 text-sm"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $member->first_name }} {{ $member->last_name }}</p>
                                    <p class="text-xs text-gray-500">ID: {{ $member->member_id }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">{{ $member->phone }}</p>
                                <p class="text-xs text-gray-400">{{ $member->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($branch->members->count() > 5)
                    <div class="mt-4">
                        <a href="{{ route('admin.members.index') }}?branch={{ $branch->id }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            View all {{ $branch->members->count() }} members →
                        </a>
                    </div>
                @endif
            @else
                <p class="text-gray-500">No members registered in this branch.</p>
            @endif
        </div>
    </div>
</div>
@endsection