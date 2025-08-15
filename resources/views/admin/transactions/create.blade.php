@extends('layouts.admin')

@section('title', 'New Transaction')
@section('subtitle', 'Process a deposit or withdrawal')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.transactions.store') }}">
            @csrf
            <div class="space-y-6">
                <!-- Savings Account -->
                <div>
                    <x-required-label for="savings_account_id" value="Savings Account" />
                    <select name="savings_account_id" id="savings_account_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Account</option>
                        @foreach($savingsAccounts as $account)
                            <option value="{{ $account->id }}" {{ old('savings_account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->account_number }} - {{ $account->member->first_name }} {{ $account->member->last_name }} (Balance: रू {{ number_format($account->balance, 2) }})
                            </option>
                        @endforeach
                    </select>
                    @error('savings_account_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Transaction Type -->
                <div>
                    <x-required-label for="type" value="Transaction Type" />
                    <select name="type" id="type" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Type</option>
                        <option value="deposit" {{ old('type') == 'deposit' ? 'selected' : '' }}>Deposit</option>
                        <option value="withdrawal" {{ old('type') == 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount -->
                <div>
                    <x-required-label for="amount" value="Amount (रू)" />
                    <input type="number" name="amount" id="amount" value="{{ old('amount') }}" step="0.01" min="0.01" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <x-label for="description" value="Description (Optional)" />
                    <textarea name="description" id="description" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.transactions.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Process Transaction
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection