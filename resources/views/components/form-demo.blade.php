@extends('layouts.admin')

@section('title', 'Form Components Demo')
@section('subtitle', 'Demonstration of form components with validation')

@section('content')
<div class="max-w-4xl mx-auto">
    @if(session('success'))
        <x-form-alert type="success" :message="session('success')" />
    @endif
    
    @if(session('error'))
        <x-form-alert type="error" :message="session('error')" />
    @endif
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold">Form Components Demo</h2>
            <a href="{{ route('admin.form-documentation') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-book mr-1"></i> View Documentation
            </a>
        </div>
        <form method="POST" action="{{ route('admin.form-demo') }}">
            @csrf
            
            <x-form-group>
                <!-- Text Input -->
                <x-form-input
                    name="name"
                    label="Name"
                    required
                    placeholder="Enter your name"
                    :value="old('name')"
                />
                
                <!-- Email Input -->
                <x-form-input
                    type="email"
                    name="email"
                    label="Email Address"
                    required
                    placeholder="your@email.com"
                    :value="old('email')"
                />
                
                <!-- Number Input -->
                <x-form-input
                    type="number"
                    name="age"
                    label="Age"
                    min="18"
                    max="100"
                    :value="old('age')"
                />
                
                <!-- Select Dropdown -->
                <x-form-select
                    name="country"
                    label="Country"
                    required
                >
                    <option value="">Select a country</option>
                    <option value="us" {{ old('country') == 'us' ? 'selected' : '' }}>United States</option>
                    <option value="ca" {{ old('country') == 'ca' ? 'selected' : '' }}>Canada</option>
                    <option value="uk" {{ old('country') == 'uk' ? 'selected' : '' }}>United Kingdom</option>
                </x-form-select>
                
                <!-- Textarea -->
                <x-form-textarea
                    name="bio"
                    label="Biography"
                    rows="4"
                    placeholder="Tell us about yourself"
                    :value="old('bio')"
                />
                
                <!-- Radio Group -->
                <x-form-radio-group name="gender" label="Gender" required>
                    <x-form-radio
                        name="gender"
                        value="male"
                        label="Male"
                        :checked="old('gender') == 'male'"
                    />
                    <x-form-radio
                        name="gender"
                        value="female"
                        label="Female"
                        :checked="old('gender') == 'female'"
                    />
                    <x-form-radio
                        name="gender"
                        value="other"
                        label="Other"
                        :checked="old('gender') == 'other'"
                    />
                </x-form-radio-group>
                
                <!-- Checkbox -->
                <x-form-checkbox
                    name="terms"
                    label="I agree to the terms and conditions"
                    required
                    :checked="old('terms')"
                />
                
                <!-- Form Actions -->
                <x-form-actions>
                    <x-form-button color="gray">
                        Cancel
                    </x-form-button>
                    <x-form-button>
                        Submit
                    </x-form-button>
                </x-form-actions>
            </x-form-group>
        </form>
    </div>
</div>
@endsection