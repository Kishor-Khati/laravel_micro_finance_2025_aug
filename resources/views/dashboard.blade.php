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
