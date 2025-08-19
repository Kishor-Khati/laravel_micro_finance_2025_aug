@extends('layouts.admin')

@section('title', 'Edit Savings Account')
@section('subtitle', 'Update savings account information')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.savings.update', $savingsAccount) }}">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                <!-- Member Selection -->
                <div>
                    <label for="member_id" class="block text-sm font-medium text-gray-700">Member</label>
                    <select name="member_id" id="member_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Member</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ old('member_id', $savingsAccount->member_id) == $member->id ? 'selected' : '' }}>
                                {{ $member->first_name }} {{ $member->last_name }} ({{ $member->member_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('member_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Savings Type -->
                <div>
                    <label for="savings_type_id" class="block text-sm font-medium text-gray-700">Savings Type</label>
                    <select name="savings_type_id" id="savings_type_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Savings Type</option>
                        @foreach($savingsTypes as $type)
                            <option value="{{ $type->id }}" {{ old('savings_type_id', $savingsAccount->savings_type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} ({{ $type->interest_rate }}% interest, Min: रू {{ $type->min_balance }})
                            </option>
                        @endforeach
                    </select>
                    @error('savings_type_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Branch -->
                <div>
                    <label for="branch_id" class="block text-sm font-medium text-gray-700">Branch</label>
                    <select name="branch_id" id="branch_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Branch</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $savingsAccount->branch_id) == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Number -->
                <div>
                    <label for="account_number" class="block text-sm font-medium text-gray-700">Account Number</label>
                    <input type="text" name="account_number" id="account_number" value="{{ old('account_number', $savingsAccount->account_number) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-sm text-gray-500">Unique account identifier</p>
                    @error('account_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Balance -->
                <div>
                    <label for="balance" class="block text-sm font-medium text-gray-700">Current Balance (रू)</label>
                    <input type="number" name="balance" id="balance" value="{{ old('balance', $savingsAccount->balance) }}" step="0.01" min="0" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-sm text-gray-500">Current account balance</p>
                    @error('balance')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Interest Earned -->
                <div>
                    <label for="interest_earned" class="block text-sm font-medium text-gray-700">Interest Earned (रू)</label>
                    <input type="number" name="interest_earned" id="interest_earned" value="{{ old('interest_earned', $savingsAccount->interest_earned) }}" step="0.01" min="0" readonly
                        class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-sm text-gray-500">Total interest earned on this account</p>
                    @error('interest_earned')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Opened Date -->
                <div>
                    <label for="opened_date" class="block text-sm font-medium text-gray-700">Account Opened Date</label>
                    <input type="date" 
                           id="opened_date" 
                           name="opened_date" 
                           value="{{ old('opened_date', $savingsAccount->opened_date?->format('Y-m-d')) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           required>
                    @error('opened_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Account Status</label>
                    <select name="status" id="status" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="active" {{ old('status', $savingsAccount->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $savingsAccount->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="closed" {{ old('status', $savingsAccount->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-md font-medium text-gray-900 mb-3">Account Information</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Created:</span>
                            <span class="font-medium">{{ $savingsAccount->created_at->format('M d, Y') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-medium">{{ $savingsAccount->updated_at->format('M d, Y') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Total Transactions:</span>
                            <span class="font-medium">{{ $savingsAccount->transactions->count() }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Account Age:</span>
                            <span class="font-medium">{{ $savingsAccount->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('admin.savings.show', $savingsAccount) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Update Account
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection