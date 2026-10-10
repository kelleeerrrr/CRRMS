@props([
    'title' => 'No data found',
    'description' => 'Get started by creating a new item.',
    'icon' => 'inbox',
    'action' => null,
])

<div class="text-center py-8">
    <x-heroicon-o-{{ $icon }} class="w-10 h-10 mx-auto text-gray-400 mb-3" />
    <h3 class="text-base font-medium text-gray-900 dark:text-white mb-1">{{ $title }}</h3>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ $description }}</p>
    @if($action)
        {{ $action }}
    @endif
</div>
