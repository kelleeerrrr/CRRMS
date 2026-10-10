<x-layouts.app>
    <x-slot name="title">Server Error</x-slot>

    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4">
        <div class="max-w-md w-full text-center">
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 dark:bg-red-900 mb-6">
                    <x-heroicon-o-exclamation-circle class="w-8 h-8 text-red-600 dark:text-red-400" />
                </div>
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">500</h1>
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300">Server Error</h2>
            </div>
            
            <p class="text-gray-600 dark:text-gray-400 mb-8">
                Something went wrong on our end. Please try again later or contact support if the problem persists.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    <x-heroicon-o-home class="w-5 h-5 mr-2" />
                    Go to Dashboard
                </a>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    <x-heroicon-o-arrow-path class="w-5 h-5 mr-2" />
                    Try Again
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
