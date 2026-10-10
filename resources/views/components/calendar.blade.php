@props([
    'events' => [],
    'date' => null,
])

@php
    $date = $date ?? now();
    $currentMonth = $date->format('F Y');
    $startOfMonth = $date->copy()->startOfMonth();
    $endOfMonth = $date->copy()->endOfMonth();
    $startOfWeek = $startOfMonth->copy()->startOfWeek();
    $endOfWeek = $endOfMonth->copy()->endOfWeek();
    
    $days = [];
    $current = $startOfWeek->copy();
    
    while($current <= $endOfWeek) {
        $days[] = [
            'date' => $current->copy(),
            'isCurrentMonth' => $current->month === $date->month,
            'isToday' => $current->isToday(),
            'events' => collect($events)->filter(fn($event) => $event['date']->isSameDay($current))->values()->toArray(),
        ];
        $current->addDay();
    }
@endphp

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <!-- Header -->
    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $currentMonth }}</h3>
        <div class="flex items-center space-x-2">
            <button wire:click="previousMonth" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <x-heroicon-o-chevron-left class="w-5 h-5 text-gray-500 dark:text-gray-400" />
            </button>
            <button wire:click="nextMonth" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <x-heroicon-o-chevron-right class="w-5 h-5 text-gray-500 dark:text-gray-400" />
            </button>
        </div>
    </div>
    
    <!-- Days of Week -->
    <div class="grid grid-cols-7 border-b border-gray-200 dark:border-gray-700">
        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
            <div class="p-2 text-center text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ $day }}
            </div>
        @endforeach
    </div>
    
    <!-- Calendar Grid -->
    <div class="grid grid-cols-7">
        @foreach($days as $day)
            <div class="min-h-24 p-2 border-b border-r border-gray-200 dark:border-gray-700 {{ !$day['isCurrentMonth'] ? 'bg-gray-50 dark:bg-gray-900/50' : '' }}">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm {{ $day['isToday'] ? 'bg-blue-600 text-white w-6 h-6 rounded-full flex items-center justify-center' : ($day['isCurrentMonth'] ? 'text-gray-900 dark:text-white' : 'text-gray-400') }}">
                        {{ $day['date']->format('j') }}
                    </span>
                </div>
                <div class="space-y-1">
                    @foreach($day['events'] as $event)
                        <div class="text-xs p-1 rounded {{ $event['color'] ?? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' }} truncate">
                            {{ $event['title'] }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
