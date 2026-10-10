<x-slot name="title">Service Requests</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Service Requests', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Service Requests</h1>
            <p class="text-gray-600 dark:text-gray-400">Manage cleaning, setup, and technical support requests</p>
        </div>
        <a href="{{ route('service-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <x-heroicon-o-plus class="w-5 h-5 mr-2" />
            Add Service Request
        </a>
    </div>
</x-slot>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['total'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                <x-heroicon-o-clipboard-document-list class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            </div>
        </div>
    </x-card>
    
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pending</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['pending'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center">
                <x-heroicon-o-clock class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
            </div>
        </div>
    </x-card>
    
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">In Progress</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['in_progress'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                <x-heroicon-o-arrow-path class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            </div>
        </div>
    </x-card>
    
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">High Priority</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['high_priority'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-red-600 dark:text-red-400" />
            </div>
        </div>
    </x-card>
</div>

<livewire:service-request-index />
