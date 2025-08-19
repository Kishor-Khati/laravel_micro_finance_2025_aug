<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-2">Welcome to Nepali Microfinance System!</h3>
                    <p class="text-gray-600 dark:text-gray-400">You're successfully logged in. Choose your area to get started:</p>
                </div>
            </div>

            <!-- Simple Nepali Date Widget -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>
                            आजको मिति (Today's Date)
                        </h3>
                        <a href="{{ route('admin.calendar') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors text-sm">
                            पूर्ण पात्रो हेर्नुहोस्
                        </a>
                    </div>
                    
                    <!-- Current Date Display -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600 rounded-lg p-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-1">
                                {{ now()->format('M d, Y') }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ now()->format('M d, Y') }}
                            </div>
                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                                AD: {{ now()->format('Y-m-d') }}
                            </div>
                        </div>
                        
                        <!-- Quick Date Info -->
                        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-300">वर्ष:</span>
                                <span class="font-bold text-blue-600 dark:text-blue-400">
                                    {{ now()->format('Y') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-300">महिना:</span>
                                <span class="font-bold text-blue-600 dark:text-blue-400">
                                    {{ now()->format('m') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-300">गते:</span>
                                <span class="font-bold text-blue-600 dark:text-blue-400">
                                    {{ now()->format('d') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-300">बार:</span>
                                <span class="font-bold text-blue-600 dark:text-blue-400">
                                    {{ now()->format('l') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Access Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Admin Panel -->
                <a href="{{ route('admin.dashboard') }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                                <i class="fas fa-tachometer-alt text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Admin Panel</h4>
                                <p class="text-gray-600 dark:text-gray-400">Manage all system operations</p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Member Management -->
                <a href="{{ route('admin.members.index') }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                                <i class="fas fa-users text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Members</h4>
                                <p class="text-gray-600 dark:text-gray-400">Manage member accounts</p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Financial Services -->
                <a href="{{ route('admin.loans.index') }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                                <i class="fas fa-hand-holding-usd text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Loans</h4>
                                <p class="text-gray-600 dark:text-gray-400">Process and manage loans</p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Savings Management -->
                <a href="{{ route('admin.savings.index') }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                                <i class="fas fa-piggy-bank text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Savings</h4>
                                <p class="text-gray-600 dark:text-gray-400">Manage savings accounts</p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Transactions -->
                <a href="{{ route('admin.transactions.index') }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4">
                                <i class="fas fa-exchange-alt text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Transactions</h4>
                                <p class="text-gray-600 dark:text-gray-400">View transaction history</p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Branches -->
                <a href="{{ route('admin.branches.index') }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                                <i class="fas fa-building text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Branches</h4>
                                <p class="text-gray-600 dark:text-gray-400">Manage branch locations</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
