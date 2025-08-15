@extends('layouts.admin')

@section('title', 'SweetAlert Documentation')
@section('subtitle', 'How to use SweetAlert for CRUD operations')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">SweetAlert Integration Documentation</h2>
        
        <div class="space-y-8">
            <!-- Introduction -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Introduction</h3>
                <p class="mt-2 text-gray-600">
                    SweetAlert2 is integrated into the application to provide beautiful, responsive, customizable
                    and accessible replacement for JavaScript's popup boxes. This documentation explains how to use
                    SweetAlert for CRUD operations in your application.
                </p>
            </div>
            
            <!-- Basic Usage -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Basic Usage</h3>
                <p class="mt-2 text-gray-600">
                    The SweetAlert utility is available globally as <code>SweetAlert</code> in JavaScript. You can use it to show different types of alerts.
                </p>
                <pre class="bg-gray-100 p-4 mt-2 rounded overflow-x-auto">
// Success message
SweetAlert.success('Success!', 'Operation completed successfully.');

// Error message
SweetAlert.error('Error!', 'Something went wrong.');

// Warning message
SweetAlert.warning('Warning!', 'This action may have consequences.');

// Toast notification
SweetAlert.toast('This is a toast message!');
</pre>
            </div>
            
            <!-- Delete Confirmation -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Delete Confirmation</h3>
                <p class="mt-2 text-gray-600">
                    To use SweetAlert for delete confirmations, add the <code>delete-button</code> class and necessary data attributes to your delete links.
                </p>
                <pre class="bg-gray-100 p-4 mt-2 rounded overflow-x-auto">
&lt;a href="#" 
   class="delete-button" 
   data-url="{{ route('admin.members.destroy', 1) }}" 
   data-name="Member Name"&gt;
   Delete
&lt;/a&gt;</pre>
                <p class="mt-2 text-gray-600">
                    The <code>data-url</code> attribute should point to the delete route, and <code>data-name</code> is used in the confirmation message.
                </p>
            </div>
            
            <!-- Controller Integration -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Controller Integration</h3>
                <p class="mt-2 text-gray-600">
                    In your controllers, use session flash messages to trigger SweetAlert notifications.
                </p>
                <pre class="bg-gray-100 p-4 mt-2 rounded overflow-x-auto">
// For success messages
return redirect()->route('admin.members.index')
    ->with('success', 'Member created successfully!');

// For error messages
return redirect()->back()
    ->with('error', 'Failed to create member.');
</pre>
                <p class="mt-2 text-gray-600">
                    The <code>x-sweet-alert</code> component in the layout will automatically display these messages using SweetAlert.
                </p>
            </div>
            
            <!-- Try/Catch Pattern -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Try/Catch Pattern</h3>
                <p class="mt-2 text-gray-600">
                    Use try/catch blocks in your controllers to handle exceptions and provide appropriate feedback.
                </p>
                <pre class="bg-gray-100 p-4 mt-2 rounded overflow-x-auto">
public function store(Request $request)
{
    $validated = $request->validate([
        // validation rules
    ]);

    try {
        // Create or update record
        return redirect()->route('admin.resource.index')
            ->with('success', 'Resource created successfully!');
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Failed to create resource: ' . $e->getMessage())
            ->withInput();
    }
}</pre>
            </div>
            
            <!-- Custom Confirmations -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Custom Confirmations</h3>
                <p class="mt-2 text-gray-600">
                    For custom confirmation dialogs, use the <code>SweetAlert.confirm</code> method.
                </p>
                <pre class="bg-gray-100 p-4 mt-2 rounded overflow-x-auto">
// General confirmation
SweetAlert.confirm(
    'Confirm Action', 
    'Are you sure you want to proceed?', 
    'Yes, proceed!', 
    function() { 
        // Code to execute on confirmation
        SweetAlert.success('Done!', 'Action completed.');
    }
);</pre>
            </div>
            
            <!-- Component Reference -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Component Reference</h3>
                <p class="mt-2 text-gray-600">
                    The <code>x-sweet-alert</code> component is included in the main layout and automatically displays flash messages.
                </p>
                <pre class="bg-gray-100 p-4 mt-2 rounded overflow-x-auto">
&lt;x-sweet-alert 
    type="success" 
    title="Custom Title" 
    message="Custom message" 
    :autoshow="true" 
/&gt;</pre>
                <p class="mt-2 text-gray-600">
                    Available props: type (success, error, warning, info), title, message, autoshow
                </p>
            </div>
        </div>
        
        <div class="mt-8 border-t pt-6 flex justify-between">
            <a href="{{ route('admin.sweet-alert-demo') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-1"></i> View Demo
            </a>
        </div>
    </div>
</div>
@endsection