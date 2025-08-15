@extends('layouts.admin')

@section('title', 'SweetAlert Demo')
@section('subtitle', 'Demo of SweetAlert for CRUD operations')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold">SweetAlert Demo</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Success Alert -->
            <div class="border rounded p-4">
                <h3 class="font-medium mb-2">Success Alert</h3>
                <p class="text-sm text-gray-600 mb-3">Display a success message with a title and text.</p>
                <button 
                    type="button" 
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 focus:outline-none"
                    onclick="SweetAlert.success('Success!', 'Operation completed successfully.')"
                >
                    Show Success Alert
                </button>
            </div>
            
            <!-- Error Alert -->
            <div class="border rounded p-4">
                <h3 class="font-medium mb-2">Error Alert</h3>
                <p class="text-sm text-gray-600 mb-3">Display an error message with a title and text.</p>
                <button 
                    type="button" 
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 focus:outline-none"
                    onclick="SweetAlert.error('Error!', 'Something went wrong.')"
                >
                    Show Error Alert
                </button>
            </div>
            
            <!-- Warning Alert -->
            <div class="border rounded p-4">
                <h3 class="font-medium mb-2">Warning Alert</h3>
                <p class="text-sm text-gray-600 mb-3">Display a warning message with a title and text.</p>
                <button 
                    type="button" 
                    class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 focus:outline-none"
                    onclick="SweetAlert.warning('Warning!', 'This action may have consequences.')"
                >
                    Show Warning Alert
                </button>
            </div>
            
            <!-- Toast Notification -->
            <div class="border rounded p-4">
                <h3 class="font-medium mb-2">Toast Notification</h3>
                <p class="text-sm text-gray-600 mb-3">Display a small toast notification.</p>
                <button 
                    type="button" 
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none"
                    onclick="SweetAlert.toast('This is a toast message!')"
                >
                    Show Toast
                </button>
            </div>
            
            <!-- Delete Confirmation -->
            <div class="border rounded p-4">
                <h3 class="font-medium mb-2">Delete Confirmation</h3>
                <p class="text-sm text-gray-600 mb-3">Show a confirmation dialog for delete operations.</p>
                <button 
                    type="button" 
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 focus:outline-none"
                    onclick="SweetAlert.deleteConfirm('Delete Item', 'Are you sure you want to delete this item?', function() { SweetAlert.success('Deleted!', 'Item has been deleted.'); })"
                >
                    Delete Item
                </button>
            </div>
            
            <!-- General Confirmation -->
            <div class="border rounded p-4">
                <h3 class="font-medium mb-2">General Confirmation</h3>
                <p class="text-sm text-gray-600 mb-3">Show a confirmation dialog for general operations.</p>
                <button 
                    type="button" 
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none"
                    onclick="SweetAlert.confirm('Confirm Action', 'Are you sure you want to proceed?', 'Yes, proceed!', function() { SweetAlert.success('Done!', 'Action completed.'); })"
                >
                    Confirm Action
                </button>
            </div>
        </div>
        
        <div class="mt-8 border-t pt-6">
            <h3 class="font-medium mb-4">Using with Delete Buttons</h3>
            <p class="text-sm text-gray-600 mb-4">Add the <code>delete-button</code> class and <code>data-url</code> attribute to any delete button:</p>
            
            <pre class="bg-gray-100 p-4 rounded overflow-x-auto mb-4">&lt;a href="#" 
   class="delete-button" 
   data-url="{{ route('admin.members.destroy', 1) }}" 
   data-name="Member Name"&gt;
   Delete
&lt;/a&gt;</pre>
            
            <div class="mt-4">
                <a href="#" 
                   class="delete-button px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 focus:outline-none" 
                   data-url="#" 
                   data-name="Demo Item">
                   Delete Demo Item
                </a>
            </div>
        </div>
        
        <div class="mt-8 border-t pt-6">
            <h3 class="font-medium mb-4">Using in Controllers</h3>
            <p class="text-sm text-gray-600 mb-4">In your controller, simply return with a session flash message:</p>
            
            <pre class="bg-gray-100 p-4 rounded overflow-x-auto">
// For success messages
return redirect()->route('admin.members.index')
    ->with('success', 'Member created successfully!');

// For error messages
return redirect()->back()
    ->with('error', 'Failed to create member.');
</pre>
        </div>
        <div class="mt-8 border-t pt-6 flex justify-between">
            <p class="text-gray-600 italic">Note: This is a demo page. In a real application, these alerts would be triggered by actual CRUD operations.</p>
            <a href="{{ route('admin.sweet-alert-documentation') }}" class="text-blue-600 hover:text-blue-800">
                View Documentation <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
</div>
@endsection