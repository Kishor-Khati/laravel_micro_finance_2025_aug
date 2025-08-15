<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('app.title') }} - Laravel 11</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div class="min-h-screen flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center py-6">
                        <div class="flex items-center">
                            <i class="fas fa-university text-3xl text-blue-600 mr-3"></i>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ __('app.title') }}</h1>
                                <p class="text-sm text-gray-600">{{ __('app.subtitle') }}</p>
                            </div>
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
                                Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <!-- Success Message -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                            <div>
                                <h3 class="text-lg font-semibold text-green-800">{{ __('app.success_title') }}</h3>
                                <p class="text-green-700 mt-1">{{ __('app.success_message') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Database Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex items-center">
                                <i class="fas fa-building text-blue-500 text-2xl mr-4"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">{{ __('app.branches') }}</h4>
                                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Branch::count() }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex items-center">
                                <i class="fas fa-users text-green-500 text-2xl mr-4"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">{{ __('app.members') }}</h4>
                                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Member::count() }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex items-center">
                                <i class="fas fa-hand-holding-usd text-purple-500 text-2xl mr-4"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">{{ __('app.loan_types') }}</h4>
                                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\LoanType::count() }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex items-center">
                                <i class="fas fa-piggy-bank text-yellow-500 text-2xl mr-4"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">{{ __('app.savings_types') }}</h4>
                                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\SavingsType::count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Features Overview -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Backend Features -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-server text-blue-500 mr-2"></i>
                                {{ __('app.backend_implementation') }}
                            </h3>
                            <ul class="space-y-3">
                                <li class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    {{ __('app.complete_mvc') }}
                                </li>
                                <li class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    {{ __('app.database_migrations') }}
                                </li>
                                <li class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    {{ __('app.eloquent_models') }}
                                </li>
                                <li class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    {{ __('app.restful_controllers') }}
                                </li>
                                <li class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    {{ __('app.database_seeders') }}
                                </li>
                                <li class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    {{ __('app.role_based_auth') }}
                                </li>
                            </ul>
                        </div>

                        <!-- Available Modules -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-puzzle-piece text-purple-500 mr-2"></i>
                                {{ __('app.available_modules') }}
                            </h3>
                            <ul class="space-y-3">
                                <li class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-user-plus text-blue-500 mr-2"></i>
                                    {{ __('app.member_management') }}
                                </li>
                                <li class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-money-bill-wave text-green-500 mr-2"></i>
                                    {{ __('app.loan_processing') }}
                                </li>
                                <li class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-piggy-bank text-yellow-500 mr-2"></i>
                                    {{ __('app.savings_management') }}
                                </li>
                                <li class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-exchange-alt text-purple-500 mr-2"></i>
                                    {{ __('app.transaction_processing') }}
                                </li>
                                <li class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-receipt text-red-500 mr-2"></i>
                                    {{ __('app.expense_management') }}
                                </li>
                                <li class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-chart-dashboard text-indigo-500 mr-2"></i>
                                    {{ __('app.analytics_dashboard') }}
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Sample Data Overview -->
                    <div class="mt-8 bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-database text-green-500 mr-2"></i>
                            {{ __('app.sample_data_loaded') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Branches -->
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">{{ __('app.branches') }}</h4>
                                @foreach(\App\Models\Branch::take(3)->get() as $branch)
                                <div class="text-sm text-gray-600 mb-1">
                                    <i class="fas fa-building mr-1"></i>
                                    {{ $branch->name }} ({{ $branch->code }})
                                </div>
                                @endforeach
                            </div>

                            <!-- Loan Types -->
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">{{ __('app.loan_types') }}</h4>
                                @foreach(\App\Models\LoanType::take(3)->get() as $loanType)
                                <div class="text-sm text-gray-600 mb-1">
                                    <i class="fas fa-hand-holding-usd mr-1"></i>
                                    {{ $loanType->name }} ({{ $loanType->interest_rate }}%)
                                </div>
                                @endforeach
                            </div>

                            <!-- Savings Types -->
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">{{ __('app.savings_types') }}</h4>
                                @foreach(\App\Models\SavingsType::take(3)->get() as $savingsType)
                                <div class="text-sm text-gray-600 mb-1">
                                    <i class="fas fa-piggy-bank mr-1"></i>
                                    {{ $savingsType->name }} ({{ $savingsType->interest_rate }}%)
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Default Users -->
                    <div class="mt-8 bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-users-cog text-blue-500 mr-2"></i>
                            {{ __('app.default_user_accounts') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach(\App\Models\User::whereNot('role', 'member')->get() as $user)
                            <div class="border rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    @switch($user->role)
                                        @case('super_admin')
                                            <i class="fas fa-crown text-yellow-500 mr-2"></i>
                                            @break
                                        @case('branch_manager')
                                            <i class="fas fa-user-tie text-blue-500 mr-2"></i>
                                            @break
                                        @case('field_officer')
                                            <i class="fas fa-user-hard-hat text-green-500 mr-2"></i>
                                            @break
                                        @case('accountant')
                                            <i class="fas fa-calculator text-purple-500 mr-2"></i>
                                            @break
                                    @endswitch
                                    <span class="font-medium text-gray-900">{{ ucwords(str_replace('_', ' ', $user->role)) }}</span>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <div><strong>{{ __('app.email') }}:</strong> {{ $user->email }}</div>
                                    <div><strong>{{ __('app.password') }}:</strong> password</div>
                                    @if($user->branch)
                                    <div><strong>{{ __('app.branch') }}:</strong> {{ $user->branch->name }}</div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Next Steps -->
                    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-blue-800 mb-4">
                            <i class="fas fa-rocket text-blue-600 mr-2"></i>
                            {{ __('app.next_steps') }}
                        </h3>
                        <ul class="space-y-2 text-blue-700">
                            <li class="flex items-center">
                                <i class="fas fa-arrow-right mr-2"></i>
                                {{ __('app.install_breeze') }}
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-arrow-right mr-2"></i>
                                {{ __('app.create_blade_templates') }}
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-arrow-right mr-2"></i>
                                {{ __('app.implement_frontend') }}
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-arrow-right mr-2"></i>
                                {{ __('app.add_validation') }}
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-arrow-right mr-2"></i>
                                {{ __('app.integrate_templates') }}
                            </li>
                        </ul>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center text-sm text-gray-500">
                        {{ __('app.footer_text') }} {{ Illuminate\Foundation\Application::VERSION }} - {{ __('app.built_with') }} {{ PHP_VERSION }}
                    </div>
                </div>
            </footer>
        </div>

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Language Switcher JavaScript -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const languageSelector = document.getElementById('languageSelector');
                
                if (languageSelector) {
                    languageSelector.addEventListener('change', function() {
                        const selectedLanguage = this.value;
                        switchLanguage(selectedLanguage);
                    });
                }
            });

            function switchLanguage(language) {
                // Create a form and submit it to switch language
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("language.switch") }}';
                
                // Add CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);
                
                // Add language parameter
                const languageInput = document.createElement('input');
                languageInput.type = 'hidden';
                languageInput.name = 'language';
                languageInput.value = language;
                form.appendChild(languageInput);
                
                // Submit the form
                document.body.appendChild(form);
                form.submit();
            }
        </script>
    </body>
</html>