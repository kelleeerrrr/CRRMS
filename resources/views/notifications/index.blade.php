<x-app-layout>
    <x-slot name="title">Notifications</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notifications</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage your notifications</p>
            </div>
            @if(auth()->user()->can('create', \App\Models\Notification::class))
                <a href="{{ route('notifications.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                    Create Notification
                </a>
            @endif
        </div>
    </x-slot>

    <livewire:notification-index />
</x-app-layout>
