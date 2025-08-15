@extends('layouts.admin')

@section('title', 'Edit Branch')
@section('subtitle', 'Update branch information')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.branches.update', $branch) }}">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                <!-- Branch Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Branch Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $branch->name) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Branch Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">Branch Code</label>
                    <input type="text" name="code" id="code" value="{{ old('code', $branch->code) }}" required maxlength="10"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-sm text-gray-500">Unique branch identifier (max 10 characters)</p>
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea name="address" id="address" rows="3" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $branch->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $branch->phone) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $branch->email) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Manager Name -->
                <div>
                    <label for="manager_name" class="block text-sm font-medium text-gray-700">Manager Name</label>
                    <input type="text" name="manager_name" id="manager_name" value="{{ old('manager_name', $branch->manager_name) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('manager_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.branches.show', $branch) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Update Branch
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection