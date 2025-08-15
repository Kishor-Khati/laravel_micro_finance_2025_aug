@extends('layouts.admin')

@section('title', 'New Loan Application')
@section('subtitle', 'Process a new loan for a member')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.loans.store') }}">
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

                <!-- Loan Type -->
                <div>
                    <label for="loan_type_id" class="block text-sm font-medium text-gray-700">Loan Type</label>
                    <select name="loan_type_id" id="loan_type_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Loan Type</option>
                        @foreach($loanTypes as $type)
                            <option value="{{ $type->id }}" {{ old('loan_type_id') == $type->id ? 'selected' : '' }}>
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
                    <input type="number" name="amount" id="amount" value="{{ old('amount') }}" step="0.01" min="0" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Interest Rate -->
                <div>
                    <label for="interest_rate" class="block text-sm font-medium text-gray-700">Interest Rate (%)</label>
                    <input type="number" name="interest_rate" id="interest_rate" value="{{ old('interest_rate') }}" step="0.01" min="0" max="100" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('interest_rate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Loan Term -->
                <div>
                    <label for="term_months" class="block text-sm font-medium text-gray-700">Loan Term (Months)</label>
                    <input type="number" name="term_months" id="term_months" value="{{ old('term_months') }}" min="1" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('term_months')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Purpose -->
                <div>
                    <label for="purpose" class="block text-sm font-medium text-gray-700">Loan Purpose</label>
                    <textarea name="purpose" id="purpose" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('purpose') }}</textarea>
                    @error('purpose')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="defaulted" {{ old('status') == 'defaulted' ? 'selected' : '' }}>Defaulted</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Loan Calculation Preview -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-md font-medium text-gray-900 mb-3">Loan Calculation Preview</h4>
                    <div id="loan-calculation" class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Monthly Payment:</span>
                            <span id="monthly-payment" class="font-medium">रू 0.00</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Total Interest:</span>
                            <span id="total-interest" class="font-medium">रू 0.00</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Total Amount:</span>
                            <span id="total-amount" class="font-medium">रू 0.00</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Total Payments:</span>
                            <span id="total-payments" class="font-medium">0</span>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('admin.loans.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Create Loan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const interestInput = document.getElementById('interest_rate');
    const termInput = document.getElementById('term_months');
    
    function calculateLoan() {
        const amount = parseFloat(amountInput.value) || 0;
        const interestRate = parseFloat(interestInput.value) || 0;
        const termMonths = parseInt(termInput.value) || 0;
        
        if (amount > 0 && interestRate > 0 && termMonths > 0) {
            const totalInterest = (amount * interestRate / 100);
            const totalAmount = amount + totalInterest;
            const monthlyPayment = totalAmount / termMonths;
            
            document.getElementById('monthly-payment').textContent = 'रू ' + monthlyPayment.toFixed(2);
            document.getElementById('total-interest').textContent = 'रू ' + totalInterest.toFixed(2);
            document.getElementById('total-amount').textContent = 'रू ' + totalAmount.toFixed(2);
            document.getElementById('total-payments').textContent = termMonths;
        } else {
            document.getElementById('monthly-payment').textContent = 'रू 0.00';
            document.getElementById('total-interest').textContent = 'रू 0.00';
            document.getElementById('total-amount').textContent = 'रू 0.00';
            document.getElementById('total-payments').textContent = '0';
        }
    }
    
    amountInput.addEventListener('input', calculateLoan);
    interestInput.addEventListener('input', calculateLoan);
    termInput.addEventListener('input', calculateLoan);
    
    calculateLoan();
});
</script>
@endsection