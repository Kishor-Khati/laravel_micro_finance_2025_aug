@props([
    'message' => session('success') ?? session('error') ?? null,
    'type' => session('success') ? 'success' : (session('error') ? 'error' : 'info'),
    'title' => session('success') ? 'Success!' : (session('error') ? 'Error!' : 'Information'),
    'autoshow' => true
])

@if($message && $autoshow)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof SweetAlert !== 'undefined') {
            @if($type === 'success')
                SweetAlert.success('{{ $title }}', '{{ $message }}');
            @elseif($type === 'error')
                SweetAlert.error('{{ $title }}', '{{ $message }}');
            @elseif($type === 'warning')
                SweetAlert.warning('{{ $title }}', '{{ $message }}');
            @else
                SweetAlert.toast('{{ $message }}', '{{ $type }}');
            @endif
        } else {
            console.error('SweetAlert is not defined. Make sure the JavaScript is loaded properly.');
        }
    });
</script>
@endif

{{-- Delete Confirmation Template --}}
<template id="delete-confirmation-template">
    <form method="POST" class="delete-form">
        @csrf
        @method('DELETE')
    </form>
</template>

{{-- Global SweetAlert Scripts --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle delete confirmations
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const url = this.getAttribute('data-url');
                const name = this.getAttribute('data-name') || 'this item';
                
                if (typeof SweetAlert !== 'undefined') {
                    SweetAlert.deleteConfirm(
                        'Delete Confirmation',
                        `Are you sure you want to delete ${name}?`,
                        function() {
                            const template = document.getElementById('delete-confirmation-template');
                            const form = template.content.querySelector('.delete-form').cloneNode(true);
                            form.action = url;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    );
                } else {
                    if (confirm(`Are you sure you want to delete ${name}?`)) {
                        const template = document.getElementById('delete-confirmation-template');
                        const form = template.content.querySelector('.delete-form').cloneNode(true);
                        form.action = url;
                        document.body.appendChild(form);
                        form.submit();
                    }
                }
            });
        });
    });
</script>