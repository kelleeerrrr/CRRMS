@props([
    'notifications' => [],
    'title' => null,
    'loading' => false,
])

<x-card>
    <div class="flex items-center justify-between mb-4">
        @if($title)
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
        @endif
        <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
            View All
        </a>
    </div>

    @if($loading)
        <div class="space-y-4">
            @for($i = 0; $i < 5; $i++)
                <x-loading-skeleton class="h-16" />
            @endfor
        </div>
    @elseif(empty($notifications))
        <div class="text-center py-8">
            <x-heroicon-o-bell class="w-8 h-8 mx-auto mb-4 text-gray-300 dark:text-gray-600" />
            <p class="text-gray-500 dark:text-gray-400">No notifications</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($notifications as $notification)
                <div class="flex items-start p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center flex-shrink-0 mt-1">
                        <x-heroicon-o-bell class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="flex-1 min-w-0 ml-3">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $notification['title'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $notification['time'] ?? 'Just now' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-card>
