@props(['type' => 'success', 'message' => ''])

@php
    $config = [
        'success' => ['bg' => 'bg-green-50 dark:bg-green-950/40', 'border' => 'border-green-200 dark:border-green-800', 'text' => 'text-green-800 dark:text-green-200', 'icon' => 'check-circle'],
        'error' => ['bg' => 'bg-red-50 dark:bg-red-950/40', 'border' => 'border-red-200 dark:border-red-800', 'text' => 'text-red-800 dark:text-red-200', 'icon' => 'exclamation'],
        'warning' => ['bg' => 'bg-yellow-50 dark:bg-yellow-950/40', 'border' => 'border-yellow-200 dark:border-yellow-800', 'text' => 'text-yellow-800 dark:text-yellow-200', 'icon' => 'exclamation-triangle'],
        'info' => ['bg' => 'bg-blue-50 dark:bg-blue-950/40', 'border' => 'border-blue-200 dark:border-blue-800', 'text' => 'text-blue-800 dark:text-blue-200', 'icon' => 'exclamation'],
    ];
    
    $config = $config[$type] ?? $config['success'];
@endphp

<div role="{{ $type === 'error' ? 'alert' : 'status' }}" class="mb-4 p-4 rounded-xl border {{ $config['bg'] }} {{ $config['border'] }} {{ $config['text'] }} flex items-center shadow-sm">
    <x-dynamic-component :component="'heroicon-o-' . $config['icon']" class="w-5 h-5 mr-3 flex-shrink-0" />
    <p class="text-sm font-medium">{{ $message }}</p>
    <button type="button" aria-label="Dismiss message" onclick="this.parentElement.remove()" class="ml-auto p-1 rounded-lg hover:bg-black/5 dark:hover:bg-white/10 transition-colors">
        <x-heroicon-o-x-mark class="w-4 h-4 opacity-50 hover:opacity-100" />
    </button>
</div>
