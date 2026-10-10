@props(['type' => 'success', 'message' => ''])

@php
    $config = [
        'success' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-800', 'icon' => 'check-circle'],
        'error' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-800', 'icon' => 'x-circle'],
        'warning' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'text' => 'text-yellow-800', 'icon' => 'exclamation-triangle'],
        'info' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-800', 'icon' => 'information-circle'],
    ];
    
    $config = $config[$type] ?? $config['success'];
@endphp

<div class="mb-4 p-4 rounded-xl border {{ $config['bg'] }} {{ $config['border'] }} {{ $config['text'] }} flex items-center shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
    <x-heroicon-o-{{ $config['icon'] }} class="w-5 h-5 mr-3 flex-shrink-0" />
    <p class="text-sm font-medium">{{ $message }}</p>
    <button onclick="this.parentElement.remove()" class="ml-auto p-1 rounded-lg hover:bg-black/5 dark:hover:bg-white/10 transition-colors">
        <x-heroicon-o-x-mark class="w-4 h-4 opacity-50 hover:opacity-100" />
    </button>
</div>
