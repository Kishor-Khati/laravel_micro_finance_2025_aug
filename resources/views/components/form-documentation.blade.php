@extends('layouts.admin')

@section('title', 'Form Components Documentation')
@section('subtitle', 'How to use the form components with validation')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Form Components Documentation</h2>
        
        <div class="space-y-8">
            <!-- Introduction -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Introduction</h3>
                <p class="mt-2 text-gray-600">
                    These form components provide a standardized way to create forms with validation error display and old value retention.
                    They help maintain consistency across the application and reduce code duplication.
                </p>
            </div>
            
            <!-- Form Input -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Form Input</h3>
                <p class="mt-2 text-gray-600">
                    The <code>x-form-input</code> component creates an input field with label and error display.
                </p>
                <pre class="bg-gray-100 p-4 mt-2 rounded overflow-x-auto">
&lt;x-form-input
    name="email"
    label="Email Address"
    type="email"
    required
    placeholder="your@email.com"
    :value="old('email')"
/&gt;</pre>
                <p class="mt-2 text-gray-600">
                    Available props: type, name, id, value, label, required, placeholder, autocomplete, disabled, readonly, min, max, step, pattern, autofocus, class
                </p>
            </div>
            
            <!-- Form Select -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Form Select</h3>
                <p class="mt-2 text-gray-600">
                    The <code>x-form-select</code> component creates a dropdown select with label and error display.
                </p>
                <pre class="bg-gray-100 p-4 mt-2 rounded overflow-x-auto">
&lt;x-form-select
    name="country"
    label="Country"
    required
&gt;
    &lt;option value=""&gt;Select a country&lt;/option&gt;
    &lt;option value="us" {{ old('country') == 'us' ? 'selected' : '' }}&gt;United States&lt;/option&gt;
    &lt;option value="ca" {{ old('country') == 'ca' ? 'selected' : '' }}&gt;Canada&lt;/option&gt;
&lt;/x-form-select&gt;</pre>
                <p class="mt-2 text-gray-600">
                    Available props: name, id, label, required, disabled, class
                </p>
            </div>
            
            <!-- Form Textarea -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Form Textarea</h3>
                <p class="mt-2 text-gray-600">
                    The <code>x-form-textarea</code> component creates a multi-line text input with label and error display.
                </p>
                <pre class="bg-gray-100 p-4 mt-2 rounded overflow-x-auto">
&lt;x-form-textarea
    name="bio"
    label="Biography"
    rows="4"
    placeholder="Tell us about yourself"
    :value="old('bio')"
/&gt;</pre>
                <p class="mt-2 text-gray-600">
                    Available props: name, id, value, label, required, placeholder, disabled, readonly, rows, class
                </p>
            </div>
            
            <!-- Form Radio Group -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Form Radio Group</h3>
                <p class="mt-2 text-gray-600">
                    The <code>x-form-radio-group</code> component creates a group of radio buttons with a shared label and error display.
                </p>
                <pre class="bg-gray-100 p-4 mt-2 rounded overflow-x-auto">
&lt;x-form-radio-group name="gender" label="Gender" required&gt;
    &lt;x-form-radio
        name="gender"
        value="male"
        label="Male"
        :checked="old('gender') == 'male'"
    /&gt;
    &lt;x-form-radio
        name="gender"
        value="female"
        label="Female"
        :checked="old('gender') == 'female'"
    /&gt;
&lt;/x-form-radio-group&gt;</pre>
                <p class="mt-2 text-gray-600">
                    Available props for radio group: name, label, required, class<br>
                    Available props for radio: name, id, value, checked, label, required, disabled, class
                </p>
            </div>
            
            <!-- Form Checkbox -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Form Checkbox</h3>
                <p class="mt-2 text-gray-600">
                    The <code>x-form-checkbox</code> component creates a checkbox with label and error display.
                </p>
                <pre class="bg-gray-100 p-4 mt-2 rounded overflow-x-auto">
&lt;x-form-checkbox
    name="terms"
    label="I agree to the terms and conditions"
    required
    :checked="old('terms')"
/&gt;</pre>
                <p class="mt-2 text-gray-600">
                    Available props: name, id, value, checked, label, required, disabled, class
                </p>
            </div>
            
            <!-- Form Button -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Form Button</h3>
                <p class="mt-2 text-gray-600">
                    The <code>x-form-button</code> component creates a styled button for form actions.
                </p>
                <pre class="bg-gray-100 p-4 mt-2 rounded overflow-x-auto">
&lt;x-form-button type="submit" color="blue"&gt;
    Submit
&lt;/x-form-button&gt;</pre>
                <p class="mt-2 text-gray-600">
                    Available props: type (submit, button, reset), color (blue, red, green, gray), class
                </p>
            </div>
            
            <!-- Form Actions -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Form Actions</h3>
                <p class="mt-2 text-gray-600">
                    The <code>x-form-actions</code> component creates a container for form buttons with consistent spacing.
                </p>
                <pre class="bg-gray-100 p-4 mt-2 rounded overflow-x-auto">
&lt;x-form-actions&gt;
    &lt;x-form-button color="gray"&gt;Cancel&lt;/x-form-button&gt;
    &lt;x-form-button&gt;Submit&lt;/x-form-button&gt;
&lt;/x-form-actions&gt;</pre>
                <p class="mt-2 text-gray-600">
                    Available props: class
                </p>
            </div>
            
            <!-- Form Alert -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Form Alert</h3>
                <p class="mt-2 text-gray-600">
                    The <code>x-form-alert</code> component creates a styled alert message for form feedback.
                </p>
                <pre class="bg-gray-100 p-4 mt-2 rounded overflow-x-auto">
&lt;x-form-alert type="success" message="Form submitted successfully!" /&gt;

@if(session('success'))
    &lt;x-form-alert type="success" :message="session('success')" /&gt;
@endif</pre>
                <p class="mt-2 text-gray-600">
                    Available props: type (success, error, warning, info), message, dismissible, class
                </p>
            </div>
        </div>
    </div>
</div>
@endsection