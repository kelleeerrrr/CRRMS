@props(['items' => []])

<div class="relative">
    @if(empty($items))
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            <p>No timeline items</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($items as $index => $item)
                <div class="relative flex items-start">
                    @if($index !== count($items) - 1)
                        <div class="absolute left-4 top-8 -bottom-2 w-0.5 bg-gray-200 dark:bg-gray-700"></div>
                    @endif
                    
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center z-10">
                        @isset($item['icon'])
                            <x-heroicon-o-{{ $item['icon'] }} class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                        @else
                            <x-heroicon-o-check class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                        @endif
                    </div>
                    
                    <div class="ml-4 flex-1">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item['title'] }}</p>
                            @isset($item['time'])
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item['time'] }}</p>
                            @endif
                        </div>
                        @isset($item['description'])
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $item['description'] }}</p>
                        @endif
                        @isset($item['meta'])
                            <div class="mt-2 flex items-center space-x-2">
                                @foreach($item['meta'] as $meta)
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                        {{ $meta }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
