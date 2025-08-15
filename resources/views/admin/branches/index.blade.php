@extends('layouts.admin')

@section('title', 'Branch Management')
@section('subtitle', 'Manage microfinance branches')

@section('content')
<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center">
        <h3 class="text-xl font-semibold text-gray-900">All Branches</h3>
        <a href="{{ route('admin.branches.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Add Branch
        </a>
    </div>

    <!-- Branches Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($branches as $branch)
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h4 class="text-lg font-semibold text-gray-900">{{ $branch->name }}</h4>
                    <p class="text-sm text-gray-600 mb-2">Code: {{ $branch->code }}</p>
                    <p class="text-sm text-gray-700">{{ $branch->address }}</p>
                    
                    @if($branch->phone)
                        <p class="text-sm text-gray-600 mt-2">
                            <i class="fas fa-phone mr-1"></i> {{ $branch->phone }}
                        </p>
                    @endif
                    
                    @if($branch->email)
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-envelope mr-1"></i> {{ $branch->email }}
                        </p>
                    @endif

                    @if($branch->manager_name)
                        <p class="text-sm text-gray-600 mt-2">
                            <i class="fas fa-user-tie mr-1"></i> Manager: {{ $branch->manager_name }}
                        </p>
                    @endif
                </div>
            </div>

            <!-- Stats -->
            <div class="mt-4 grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                <div class="text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ $branch->users_count }}</p>
                    <p class="text-xs text-gray-600">Users</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-green-600">{{ $branch->members_count }}</p>
                    <p class="text-xs text-gray-600">Members</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-4 flex justify-between items-center pt-4 border-t border-gray-200">
                <a href="{{ route('admin.branches.show', $branch) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    <i class="fas fa-eye mr-1"></i> View
                </a>
                <div class="space-x-2">
                    <a href="{{ route('admin.branches.edit', $branch) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <form method="POST" action="{{ route('admin.branches.destroy', $branch) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm" onclick="return confirm('Are you sure?')">
                            <i class="fas fa-trash mr-1"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $branches->links() }}
    </div>
</div>
@endsection