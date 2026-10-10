<x-app-layout>
    <x-slot name="title">Announcements</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Announcements</h1>
                <p class="text-gray-600 dark:text-gray-400">Stay updated with the latest news and events</p>
            </div>
            <a href="{{ route('announcements.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                Create Announcement
            </a>
        </div>
    </x-slot>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['total'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                <x-heroicon-o-document-text class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            </div>
        </div>
    </x-card>
    
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pinned</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['pinned'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center">
                <x-heroicon-o-pin class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
            </div>
        </div>
    </x-card>
    
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Active</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['active'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                <x-heroicon-o-check-circle class="w-6 h-6 text-green-600 dark:text-green-400" />
            </div>
        </div>
    </x-card>
</div>

<livewire:announcement-index />
</x-app-layout>
