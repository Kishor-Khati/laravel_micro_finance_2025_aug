<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin Panel</title>

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
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-900 text-white fixed h-full overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center">
                    <i class="fas fa-university text-2xl text-blue-400 mr-3"></i>
                    <h1 class="text-xl font-bold">{{ __('app.title') }}</h1>
                </div>
                <p class="text-gray-400 text-sm mt-1">Admin Panel</p>
            </div>

            <!-- Navigation -->
            <nav class="mt-6">
                <div class="px-6 py-2">
                    <p class="text-gray-400 text-xs uppercase tracking-wider">Main</p>
                </div>
                
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : '' }}">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Dashboard
                </a>

                <div class="px-6 py-2 mt-6">
                    <p class="text-gray-400 text-xs uppercase tracking-wider">Management</p>
                </div>

                <a href="{{ route('admin.users') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white {{ request()->routeIs('admin.users*') ? 'bg-gray-800 text-white' : '' }}">
                    <i class="fas fa-users-cog mr-3"></i>
                    Users
                </a>

                <a href="{{ route('admin.branches.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white {{ request()->routeIs('admin.branches*') ? 'bg-gray-800 text-white' : '' }}">
                    <i class="fas fa-building mr-3"></i>
                    Branches
                </a>

                <a href="{{ route('admin.members.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white {{ request()->routeIs('admin.members*') ? 'bg-gray-800 text-white' : '' }}">
                    <i class="fas fa-users mr-3"></i>
                    Members
                </a>

                <div class="px-6 py-2 mt-6">
                    <p class="text-gray-400 text-xs uppercase tracking-wider">Financial</p>
                </div>

                <a href="{{ route('admin.loans.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white {{ request()->routeIs('admin.loans*') ? 'bg-gray-800 text-white' : '' }}">
                    <i class="fas fa-hand-holding-usd mr-3"></i>
                    Loans
                </a>

                <a href="{{ route('admin.savings.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white {{ request()->routeIs('admin.savings*') ? 'bg-gray-800 text-white' : '' }}">
                    <i class="fas fa-piggy-bank mr-3"></i>
                    Savings
                </a>

                <a href="{{ route('admin.transactions.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white {{ request()->routeIs('admin.transactions*') ? 'bg-gray-800 text-white' : '' }}">
                    <i class="fas fa-exchange-alt mr-3"></i>
                    Transactions
                </a>

                <a href="{{ route('admin.expenses.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white {{ request()->routeIs('admin.expenses*') ? 'bg-gray-800 text-white' : '' }}">
                    <i class="fas fa-receipt mr-3"></i>
                    Expenses
                </a>

                <div class="px-6 py-2 mt-6">
                    <p class="text-gray-400 text-xs uppercase tracking-wider">Analytics</p>
                </div>

                <a href="{{ route('admin.reports.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white {{ request()->routeIs('admin.reports*') ? 'bg-gray-800 text-white' : '' }}">
                    <i class="fas fa-chart-bar mr-3"></i>
                    Reports
                </a>

                <div class="px-6 py-2 mt-6">
                    <p class="text-gray-400 text-xs uppercase tracking-wider">Settings</p>
                </div>

                <a href="{{ route('profile.edit') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-user-cog mr-3"></i>
                    Profile
                </a>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white text-left">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        Logout
                    </button>
                </form>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 ml-64">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-900">@yield('title', 'Admin Dashboard')</h2>
                            <p class="text-gray-600">@yield('subtitle', 'Manage your microfinance operations')</p>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <!-- Language Switcher -->
                            <div class="relative">
                                <select id="languageSelector" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>English</option>
                                    <option value="ne" {{ app()->getLocale() == 'ne' ? 'selected' : '' }}>नेपाली</option>
                                </select>
                            </div>
                            
                            <div class="text-sm text-gray-500">
                                <span class="font-medium">{{ auth()->user()->name }}</span>
                                <span class="text-gray-400">·</span>
                                <span class="capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="p-6">
                <!-- Session messages will be handled by SweetAlert2 -->

                @if ($errors->any())
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
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

            // Language selector handler
            const languageSelector = document.getElementById('languageSelector');
            
            if (languageSelector) {
                languageSelector.addEventListener('change', function() {
                    const selectedLanguage = this.value;
                    switchLanguage(selectedLanguage);
                });
            }
        });

        function switchLanguage(language) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("language.switch") }}';
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            
            const languageInput = document.createElement('input');
            languageInput.type = 'hidden';
            languageInput.name = 'language';
            languageInput.value = language;
            form.appendChild(languageInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>