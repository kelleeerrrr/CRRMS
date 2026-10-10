<x-app-layout>
    <x-slot name="title">Chat</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Chat</h1>
                <p class="text-gray-600 dark:text-gray-400">Real-time messaging</p>
            </div>
            <a href="{{ route('chat.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                New Chat
            </a>
        </div>
    </x-slot>

    <livewire:chat-index />
</x-app-layout>
