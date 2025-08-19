@extends('layouts.admin')

@section('title', ' Nepali Calendar')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        <i class="fas fa-calendar-alt mr-3 text-blue-600"></i>
                         Nepali Calendar
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">
                       Complete Nepali Calendar & Date Conversion
                    </p>
                </div>
                {{-- <a href="{{ route('admin.dashboard') }}" 
                   class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    ड्यासबोर्डमा फर्कनुहोस्
                </a> --}}
            </div>
        </div>
    </div>

    <!-- Current Date Info -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
               Today's Date
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400">बिक्रम संवत्</div>
                    <div class="text-xl font-bold text-blue-600 dark:text-blue-400">
                        {{ now()->format('M d, Y') }}
                    </div>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400">नेपाली मिति</div>
                    <div class="text-xl font-bold text-green-600 dark:text-green-400">
                        {{ now()->format('M d, Y') }}
                    </div>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400">ग्रेगोरियन मिति</div>
                    <div class="text-xl font-bold text-purple-600 dark:text-purple-400">
                        {{ now()->format('Y-m-d') }}
                    </div>
                </div>
                <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400">बार</div>
                    <div class="text-xl font-bold text-orange-600 dark:text-orange-400">
                        {{ now()->format('l') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Full Nepali Calendar -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            {{-- <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    पूर्ण नेपाली पात्रो (Full Nepali Calendar)
                </h3>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Powered by Nepali Patro
                </div>
            </div> --}}
            
            <!-- Calendar Container -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <div class="w-full" style="min-height: 600px;">
                    <!-- Nepali Patro Widget -->
                    <div id="np_widget_wiz1" widget="month" style="width: 100%; min-height: 600px;"></div>
                    <script async src="https://nepalipatro.com.np/np-widgets/nepalipatro.js" id="wiz1"></script>
                </div>
            </div>
            
            <!-- Calendar Features -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                    <h4 class="font-semibold text-blue-800 dark:text-blue-200 mb-2">
                        <i class="fas fa-calendar-check mr-2"></i>
                        मिति रूपान्तरण
                    </h4>
                    <p class="text-sm text-blue-600 dark:text-blue-300">
                        बिक्रम संवत् र ग्रेगोरियन मिति बीच रूपान्तरण गर्नुहोस्
                    </p>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                    <h4 class="font-semibold text-green-800 dark:text-green-200 mb-2">
                        <i class="fas fa-star mr-2"></i>
                        पञ्चाङ्ग
                    </h4>
                    <p class="text-sm text-green-600 dark:text-green-300">
                        दैनिक पञ्चाङ्ग र शुभ मुहूर्त हेर्नुहोस्
                    </p>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                    <h4 class="font-semibold text-purple-800 dark:text-purple-200 mb-2">
                        <i class="fas fa-moon mr-2"></i>
                        चाँद्र मास
                    </h4>
                    <p class="text-sm text-purple-600 dark:text-purple-300">
                        चाँद्र मास र तिथि अनुसार जानकारी
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styles for calendar widget */
#np_widget_wiz1 {
    border-radius: 8px;
    overflow: hidden;
}

/* Dark mode adjustments */
@media (prefers-color-scheme: dark) {
    #np_widget_wiz1 {
        filter: brightness(0.9) contrast(1.1);
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #np_widget_wiz1 {
        min-height: 500px;
    }
}
</style>
@endsection