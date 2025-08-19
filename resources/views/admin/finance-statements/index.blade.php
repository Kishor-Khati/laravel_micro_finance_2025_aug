@extends('layouts.admin')

@section('title', 'Finance Statements')
@section('subtitle', 'Generate financial statements with share bonus calculations')

@section('content')
<div class="space-y-6">
    <!-- Introduction Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Finance Statements</h3>
        <p class="text-gray-600 mb-4">
            Generate comprehensive financial statements with share bonus calculations for any date range.
            These statements provide a detailed breakdown of income, expenses, and share bonuses distributed to members.
        </p>
        
        <div class="bg-blue-50 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">About Share Bonuses</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Share bonuses are calculated as 30% of the total raw income (loan interest) and distributed proportionally to members based on their savings account balances.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Generate Statement Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Generate Statement</h3>
        
        <form action="{{ route('admin.finance-statements.generate') }}" method="POST" class="space-y-4">
            @csrf
            
            <!-- Date Range Options -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Range Options</label>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <input type="radio" name="date_option" id="date_range" value="date_range" checked
                            class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500" 
                            onclick="document.getElementById('date_range_fields').classList.remove('hidden');">
                        <label for="date_range" class="ml-2 block text-sm text-gray-700">Date Range</label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" name="date_option" id="all_time" value="all_time"
                            class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                            onclick="document.getElementById('date_range_fields').classList.add('hidden');">
                        <label for="all_time" class="ml-2 block text-sm text-gray-700">All Time</label>
                    </div>
                </div>
            </div>
            
            <div id="date_range_fields" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Start Date -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" 
                           id="start_date" 
                           name="start_date" 
                           value="{{ old('start_date', now()->startOfMonth()->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           required>
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" 
                           id="end_date" 
                           name="end_date" 
                           value="{{ old('end_date', now()->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           required>
                </div>
            </div>
            
            <!-- Branch Filter -->
            <div>
                <label for="branch_id" class="block text-sm font-medium text-gray-700">Branch (Optional)</label>
                <select name="branch_id" id="branch_id" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Branches</option>
                    @foreach(\App\Models\Branch::all() as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
                @error('branch_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-file-alt mr-2"></i> Generate Statement
                </button>
                <button type="submit" name="export" value="pdf" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-file-pdf mr-2"></i> Export as PDF
                </button>
                <button type="submit" name="export" value="excel" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-file-excel mr-2"></i> Export as Excel
                </button>
            </div>
        </form>
    </div>
    
    <!-- Recent Statements -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Statements</h3>
        
        <div class="bg-yellow-50 rounded-lg p-4 mb-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-lightbulb text-yellow-600 mt-1"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Tip</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Generate your first statement using the form above. Your recent statements will appear here.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection