@props([
    'actions' => [],
    'title' => null,
])

<x-card>
    <div class="flex items-center justify-between mb-4">
        @if($title)
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
        @endif
    </div>

    <div class="space-y-3">
        @foreach($actions as $action)
            <a href="{{ $action['url'] }}" class="flex items-center p-3 bg-gray-50 dark:bg-gray-800/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors group">
                <div class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mr-3 group-hover:bg-primary-200 dark:group-hover:bg-primary-900/50 transition-colors">
                    @if($action['icon'] === 'plus')
                        <x-heroicon-o-plus class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                    @elseif($action['icon'] === 'check')
                        <x-heroicon-o-check-circle class="w-5 h-5 text-success-600 dark:text-success-400" />
                    @elseif($action['icon'] === 'users')
                        <x-heroicon-o-users class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                    @elseif($action['icon'] === 'document')
                        <x-heroicon-o-document-text class="w-5 h-5 text-gold-600 dark:text-gold-400" />
                    @elseif($action['icon'] === 'chart')
                        <x-heroicon-o-chart-bar class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                    @elseif($action['icon'] === 'wrench')
                        <x-heroicon-o-wrench-screwdriver class="w-5 h-5 text-danger-600 dark:text-danger-400" />
                    @elseif($action['icon'] === 'building')
                        <x-heroicon-o-building-office class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                    @elseif($action['icon'] === 'cube')
                        <x-heroicon-o-cube class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                    @else
                        <x-heroicon-o-arrow-right class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                    @endif
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $action['title'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $action['description'] ?? '' }}</p>
                </div>
                <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors" />
            </a>
        @endforeach
    </div>
</x-card>
