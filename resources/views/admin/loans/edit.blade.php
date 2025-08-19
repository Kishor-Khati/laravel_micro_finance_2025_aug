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

                <!-- Requested Amount -->
                <div>
                    <label for="requested_amount" class="block text-sm font-medium text-gray-700">Requested Amount (रू)</label>
                    <input type="number" name="requested_amount" id="requested_amount" value="{{ old('requested_amount', $loan->requested_amount) }}" step="0.01" min="0" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('requested_amount')
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

                <!-- Duration Months -->
                <div>
                    <label for="duration_months" class="block text-sm font-medium text-gray-700">Duration (Months)</label>
                    <input type="number" name="duration_months" id="duration_months" value="{{ old('duration_months', $loan->duration_months) }}" min="1" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('duration_months')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Purpose -->
                <div>
                    <label for="purpose" class="block text-sm font-medium text-gray-700">Loan Purpose</label>
                    <textarea name="purpose" id="purpose" rows="3" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('purpose', $loan->purpose) }}</textarea>
                    @error('purpose')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Collateral -->
                <div>
                    <label for="collateral" class="block text-sm font-medium text-gray-700">Collateral</label>
                    <textarea name="collateral" id="collateral" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('collateral', $loan->collateral) }}</textarea>
                    @error('collateral')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Loan Number -->
                <div>
                    <label for="loan_number" class="block text-sm font-medium text-gray-700">Loan Number</label>
                    <input type="text" name="loan_number" id="loan_number" value="{{ old('loan_number', $loan->loan_number) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('loan_number')
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
                            <option value="{{ $branch->id }}" {{ old('branch_id', $loan->branch_id) == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Approved Amount -->
                <div>
                    <label for="approved_amount" class="block text-sm font-medium text-gray-700">Approved Amount (रू)</label>
                    <input type="number" name="approved_amount" id="approved_amount" value="{{ old('approved_amount', $loan->approved_amount) }}" step="0.01" min="0"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('approved_amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Application Date -->
                <div>
                    <label for="application_date" class="block text-sm font-medium text-gray-700">Application Date</label>
                    <input type="date" 
                           id="application_date" 
                           name="application_date" 
                           value="{{ old('application_date', $loan->application_date ? $loan->application_date->format('Y-m-d') : '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           required>
                    @error('application_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Approved Date -->
                <div>
                    <label for="approved_date" class="block text-sm font-medium text-gray-700">Approved Date</label>
                    <input type="date" 
                           id="approved_date" 
                           name="approved_date" 
                           value="{{ old('approved_date', $loan->approved_date ? $loan->approved_date->format('Y-m-d') : '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('approved_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Disbursed Date -->
                <div>
                    <label for="disbursed_date" class="block text-sm font-medium text-gray-700">Disbursed Date</label>
                    <input type="date" 
                           id="disbursed_date" 
                           name="disbursed_date" 
                           value="{{ old('disbursed_date', $loan->disbursed_date ? $loan->disbursed_date->format('Y-m-d') : '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('disbursed_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Maturity Date -->
                <div>
                    <label for="maturity_date" class="block text-sm font-medium text-gray-700">Maturity Date</label>
                    <input type="date" 
                           id="maturity_date" 
                           name="maturity_date" 
                           value="{{ old('maturity_date', $loan->maturity_date ? $loan->maturity_date->format('Y-m-d') : '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('maturity_date')
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
                        <option value="disbursed" {{ old('status', $loan->status) == 'disbursed' ? 'selected' : '' }}>Disbursed</option>
                        <option value="closed" {{ old('status', $loan->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="rejected" {{ old('status', $loan->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remarks -->
                <div>
                    <label for="remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
                    <textarea name="remarks" id="remarks" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('remarks', $loan->remarks) }}</textarea>
                    @error('remarks')
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