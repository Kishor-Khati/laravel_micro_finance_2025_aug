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
                    <label for="member_id" class="block text-sm font-medium text-gray-700">Member</label>
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
                    <label for="savings_type_id" class="block text-sm font-medium text-gray-700">Savings Type</label>
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

                <!-- Account Number is now auto-generated -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Account Number</label>
                    <div class="mt-1 block w-full py-2 px-3 bg-gray-100 rounded-md border border-gray-200">
                        <span class="text-gray-600">Will be automatically generated</span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">A unique account number will be assigned upon creation</p>
                </div>

                <!-- Initial Balance -->
                <div>
                    <label for="balance" class="block text-sm font-medium text-gray-700">Initial Balance (रू)</label>
                    <input type="number" name="balance" id="balance" value="{{ old('balance', '0') }}" step="0.01" min="0" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('balance')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Interest Rate is now derived from savings type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Interest Rate (%)</label>
                    <div class="mt-1 block w-full py-2 px-3 bg-gray-100 rounded-md border border-gray-200">
                        <span class="text-gray-600" id="interest_rate_display">Will be set based on savings type</span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Interest rate is determined by the selected savings type</p>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Account Status</label>
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
    // Display interest rate based on savings type
    const savingsTypeSelect = document.getElementById('savings_type_id');
    const interestRateDisplay = document.getElementById('interest_rate_display');
    
    const updateInterestRateDisplay = () => {
        const selectedOption = savingsTypeSelect.options[savingsTypeSelect.selectedIndex];
        if (selectedOption.value) {
            // Extract interest rate from option text
            const text = selectedOption.text;
            const match = text.match(/(\d+\.?\d*)% interest/);
            if (match) {
                interestRateDisplay.textContent = match[1] + '%';
            } else {
                interestRateDisplay.textContent = 'Will be set based on savings type';
            }
        } else {
            interestRateDisplay.textContent = 'Will be set based on savings type';
        }
    };
    
    // Update display on page load and when savings type changes
    updateInterestRateDisplay();
    savingsTypeSelect.addEventListener('change', updateInterestRateDisplay);
});
</script>
@endsection