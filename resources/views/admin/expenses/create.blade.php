@extends('layouts.admin')

@section('title', 'Add New Expense')
@section('subtitle', 'Record a new operational expense')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.expenses.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-6">
                <!-- Expense Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Expense Category</label>
                    <select name="category" id="category" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Category</option>
                        <option value="office_supplies" {{ old('category') == 'office_supplies' ? 'selected' : '' }}>Office Supplies</option>
                        <option value="utilities" {{ old('category') == 'utilities' ? 'selected' : '' }}>Utilities</option>
                        <option value="rent" {{ old('category') == 'rent' ? 'selected' : '' }}>Rent</option>
                        <option value="marketing" {{ old('category') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                        <option value="travel" {{ old('category') == 'travel' ? 'selected' : '' }}>Travel</option>
                        <option value="maintenance" {{ old('category') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="salaries" {{ old('category') == 'salaries' ? 'selected' : '' }}>Salaries</option>
                        <option value="insurance" {{ old('category') == 'insurance' ? 'selected' : '' }}>Insurance</option>
                        <option value="training" {{ old('category') == 'training' ? 'selected' : '' }}>Training</option>
                        <option value="legal" {{ old('category') == 'legal' ? 'selected' : '' }}>Legal & Professional</option>
                        <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-sm text-gray-500">Brief title for the expense</p>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Provide detailed description of the expense</p>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount and Date -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700">Amount (रू)</label>
                        <input type="number" name="amount" id="amount" value="{{ old('amount') }}" step="0.01" min="0" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="expense_date" class="block text-sm font-medium text-gray-700 mb-2">Expense Date</label>
                        <input type="date" 
                               id="expense_date" 
                               name="expense_date" 
                               value="{{ old('expense_date', now()->format('Y-m-d')) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               required>
                    </div>
                </div>

                <!-- Branch and Payment Method -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="branch_id" class="block text-sm font-medium text-gray-700">Branch</label>
                        <select name="branch_id" id="branch_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Status</option>
                            <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Receipt Number and Vendor Name -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="receipt_number" class="block text-sm font-medium text-gray-700">Receipt Number</label>
                        <input type="text" name="receipt_number" id="receipt_number" value="{{ old('receipt_number') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="mt-1 text-sm text-gray-500">Optional receipt or invoice number</p>
                        @error('receipt_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="vendor_name" class="block text-sm font-medium text-gray-700">Vendor Name</label>
                        <input type="text" name="vendor_name" id="vendor_name" value="{{ old('vendor_name') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="mt-1 text-sm text-gray-500">Optional vendor or supplier name</p>
                        @error('vendor_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Receipt Image Upload -->
                <div>
                    <label for="receipt_image" class="block text-sm font-medium text-gray-700">Receipt Image</label>
                    <input type="file" name="receipt_image" id="receipt_image" accept="image/*"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-1 text-sm text-gray-500">Upload receipt or invoice image (optional)</p>
                    @error('receipt_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expense Guidelines -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-md font-medium text-gray-900 mb-3">Expense Recording Guidelines</h4>
                    <div class="text-sm text-gray-700 space-y-2">
                        <p>• Ensure all expenses are properly categorized for accurate reporting</p>
                        <p>• Keep receipts and invoices for all recorded expenses</p>
                        <p>• Record expenses on the date they were incurred, not when paid</p>
                        <p>• Provide detailed descriptions to help with future reference</p>
                        <p>• For recurring expenses, consider setting up automated entries</p>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('admin.expenses.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Record Expense
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Quick Add Templates -->
    <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Add Templates</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button onclick="fillTemplate('utilities', 'Monthly electricity bill', 'utilities')" 
                    class="p-4 border border-gray-300 rounded-lg hover:bg-gray-50 text-left">
                <div class="font-medium text-gray-900">Utilities</div>
                <div class="text-sm text-gray-600">Electricity, water, internet</div>
            </button>
            
            <button onclick="fillTemplate('office_supplies', 'Office supplies purchase', 'office_supplies')" 
                    class="p-4 border border-gray-300 rounded-lg hover:bg-gray-50 text-left">
                <div class="font-medium text-gray-900">Office Supplies</div>
                <div class="text-sm text-gray-600">Stationery, equipment</div>
            </button>
            
            <button onclick="fillTemplate('travel', 'Business travel expense', 'travel')" 
                    class="p-4 border border-gray-300 rounded-lg hover:bg-gray-50 text-left">
                <div class="font-medium text-gray-900">Travel</div>
                <div class="text-sm text-gray-600">Transportation, accommodation</div>
            </button>
        </div>
    </div>
</div>

<script>
function fillTemplate(category, description, categoryValue) {
    document.getElementById('category').value = categoryValue;
    document.getElementById('description').value = description;
}
</script>
@endsection