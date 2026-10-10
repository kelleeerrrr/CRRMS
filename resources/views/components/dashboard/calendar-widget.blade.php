@props([
    'events' => [],
    'title' => null,
    'loading' => false,
])

<x-card>
    <div class="flex items-center justify-between mb-4">
        @if($title)
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
        @endif
        <a href="{{ route('calendar.index') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
            View Calendar
        </a>
    </div>

    @if($loading)
        <div class="space-y-4">
            @for($i = 0; $i < 5; $i++)
                <x-loading-skeleton class="h-16" />
            @endfor
        </div>
    @elseif(empty($events))
        <div class="text-center py-8">
            <x-heroicon-o-calendar class="w-8 h-8 mx-auto mb-4 text-gray-300 dark:text-gray-600" />
            <p class="text-gray-500 dark:text-gray-400">No upcoming events</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($events as $event)
                <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center mr-3 flex-shrink-0">
                        <span class="text-sm font-bold text-blue-600 dark:text-blue-400">
                            {{ \Carbon\Carbon::parse($event['date'] ?? now())->format('M') }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $event['title'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $event['time'] ?? 'All day' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-card>
