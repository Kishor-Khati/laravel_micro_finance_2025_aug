@extends('layouts.admin')

@section('title', 'Edit Loan')
@section('subtitle', 'Update loan information')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.loans.update', $loan) }}">
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
                            <option value="{{ $member->id }}" {{ old('member_id', $loan->member_id) == $member->id ? 'selected' : '' }}>
                                {{ $member->first_name }} {{ $member->last_name }} ({{ $member->member_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('member_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Loan Type -->
                <div>
                    <label for="loan_type_id" class="block text-sm font-medium text-gray-700">Loan Type</label>
                    <select name="loan_type_id" id="loan_type_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Loan Type</option>
                        @foreach($loanTypes as $type)
                            <option value="{{ $type->id }}" {{ old('loan_type_id', $loan->loan_type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} ({{ $type->min_amount }}-{{ $type->max_amount }}, {{ $type->interest_rate }}%)
                            </option>
                        @endforeach
                    </select>
                    @error('loan_type_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Loan Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Loan Amount (रू)</label>
                    <input type="number" name="amount" id="amount" value="{{ old('amount', $loan->amount) }}" step="0.01" min="0" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Interest Rate -->
                <div>
                    <label for="interest_rate" class="block text-sm font-medium text-gray-700">Interest Rate (%)</label>
                    <input type="number" name="interest_rate" id="interest_rate" value="{{ old('interest_rate', $loan->interest_rate) }}" step="0.01" min="0" max="100" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('interest_rate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Loan Term -->
                <div>
                    <label for="term_months" class="block text-sm font-medium text-gray-700">Loan Term (Months)</label>
                    <input type="number" name="term_months" id="term_months" value="{{ old('term_months', $loan->term_months) }}" min="1" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('term_months')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Purpose -->
                <div>
                    <label for="purpose" class="block text-sm font-medium text-gray-700">Loan Purpose</label>
                    <textarea name="purpose" id="purpose" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('purpose', $loan->purpose) }}</textarea>
                    @error('purpose')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="pending" {{ old('status', $loan->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ old('status', $loan->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="active" {{ old('status', $loan->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="closed" {{ old('status', $loan->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="defaulted" {{ old('status', $loan->status) == 'defaulted' ? 'selected' : '' }}>Defaulted</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('admin.loans.show', $loan) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Update Loan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection