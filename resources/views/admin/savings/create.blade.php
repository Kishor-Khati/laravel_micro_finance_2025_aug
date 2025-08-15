@extends('layouts.admin')

@section('title', 'New Savings Account')
@section('subtitle', 'Create a new savings account for a member')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.savings.store') }}">
            @csrf
            <div class="space-y-6">
                <!-- Member Selection -->
                <div>
                    <x-required-label for="member_id" value="Member" />
                    <select name="member_id" id="member_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Member</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
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
                    <x-required-label for="savings_type_id" value="Savings Type" />
                    <select name="savings_type_id" id="savings_type_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Savings Type</option>
                        @foreach($savingsTypes as $type)
                            <option value="{{ $type->id }}" {{ old('savings_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} ({{ $type->interest_rate }}% interest, Min: रू {{ $type->minimum_balance }})
                            </option>
                        @endforeach
                    </select>
                    @error('savings_type_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Number -->
                <div>
                    <x-required-label for="account_number" value="Account Number" />
                    <input type="text" name="account_number" id="account_number" value="{{ old('account_number') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-sm text-gray-500">Unique account identifier</p>
                    @error('account_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Initial Balance -->
                <div>
                    <x-required-label for="balance" value="Initial Balance (रू)" />
                    <input type="number" name="balance" id="balance" value="{{ old('balance', '0') }}" step="0.01" min="0" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('balance')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Interest Rate -->
                <div>
                    <x-required-label for="interest_rate" value="Interest Rate (%)" />
                    <input type="number" name="interest_rate" id="interest_rate" value="{{ old('interest_rate') }}" step="0.01" min="0" max="100" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('interest_rate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <x-required-label for="status" value="Account Status" />
                    <select name="status" id="status" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Terms and Conditions -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-md font-medium text-gray-900 mb-3">Account Terms</h4>
                    <div class="text-sm text-gray-700 space-y-2">
                        <p>• Interest will be calculated monthly and credited to the account</p>
                        <p>• Minimum balance requirements must be maintained as per savings type</p>
                        <p>• Withdrawal limits may apply based on savings type</p>
                        <p>• Account holder will receive monthly statements</p>
                        <p>• Any service charges will be as per current bank policy</p>
                    </div>
                    
                    <div class="mt-3">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="terms_accepted" value="1" required class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">I confirm that the member has read and accepted the terms and conditions</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('admin.savings.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Create Account
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate account number
    const generateAccountNumber = () => {
        const timestamp = Date.now().toString().slice(-6);
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        return 'SAV' + timestamp + random;
    };
    
    const accountNumberInput = document.getElementById('account_number');
    if (!accountNumberInput.value) {
        accountNumberInput.value = generateAccountNumber();
    }
    
    // Auto-fill interest rate based on savings type
    const savingsTypeSelect = document.getElementById('savings_type_id');
    const interestRateInput = document.getElementById('interest_rate');
    
    savingsTypeSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            // Extract interest rate from option text
            const text = selectedOption.text;
            const match = text.match(/(\d+\.?\d*)% interest/);
            if (match) {
                interestRateInput.value = match[1];
            }
        }
    });
});
</script>
@endsection