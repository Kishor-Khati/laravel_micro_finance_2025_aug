<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        
        <!-- SweetAlert2 -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        
        <!-- SweetAlert2 Session Messages -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Handle success messages with SweetAlert2
                @if(session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: "{{ session('success') }}",
                        timer: 3000,
                        timerProgressBar: true
                    });
                @endif

                // Handle error messages with SweetAlert2
                @if(session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: "{{ session('error') }}",
                    });
                @endif

                // Add delete confirmation for all delete forms
                document.querySelectorAll('form[method="POST"]').forEach(form => {
                    // Check if the form is a delete form (has DELETE method)
                    const methodInput = form.querySelector('input[name="_method"][value="DELETE"]');
                    if (methodInput) {
                        // Find the submit button in this form
                        const submitBtn = form.querySelector('button[type="submit"]');
                        if (submitBtn) {
                            // Remove the original onclick handler
                            submitBtn.removeAttribute('onclick');
                            
                            // Add our SweetAlert confirmation
                            submitBtn.addEventListener('click', function(e) {
                                e.preventDefault();
                                
                                Swal.fire({
                                    title: 'Are you sure?',
                                    text: "This action cannot be undone!",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#3085d6',
                                    confirmButtonText: 'Yes, delete it!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        form.submit();
                                    }
                                });
                            });
                        }
                    }
                });
            });
        </script>
    </body>
</html>
