@props([
    'activities' => [],
    'title' => null,
    'loading' => false,
])

<x-card>
    <div class="flex items-center justify-between mb-4">
        @if($title)
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
        @endif
    </div>

    @if($loading)
        <div class="space-y-4">
            @for($i = 0; $i < 5; $i++)
                <x-loading-skeleton class="h-16" />
            @endfor
        </div>
    @elseif(empty($activities))
        <div class="text-center py-8">
            <x-heroicon-o-clock class="w-8 h-8 mx-auto mb-4 text-gray-300 dark:text-gray-600" />
            <p class="text-gray-500 dark:text-gray-400">No recent activity</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($activities as $activity)
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center flex-shrink-0 mt-1">
                        <x-heroicon-o-clock class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $activity['description'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $activity['user'] }} • {{ $activity['time'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-card>
