@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
])

@php
    $variants = [
        'primary' => 'bg-primary-600 hover:bg-primary-700 text-white focus:ring-primary-500 focus:ring-offset-2 active:bg-primary-800 shadow-sm hover:shadow-md transition-all duration-200',
        'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white focus:ring-gray-500 focus:ring-offset-2 active:bg-gray-800 shadow-sm hover:shadow-md transition-all duration-200',
        'success' => 'bg-success-600 hover:bg-success-700 text-white focus:ring-success-500 focus:ring-offset-2 active:bg-success-800 shadow-sm hover:shadow-md transition-all duration-200',
        'danger' => 'bg-danger-600 hover:bg-danger-700 text-white focus:ring-danger-500 focus:ring-offset-2 active:bg-danger-800 shadow-sm hover:shadow-md transition-all duration-200',
        'warning' => 'bg-gold-600 hover:bg-gold-700 text-white focus:ring-gold-500 focus:ring-offset-2 active:bg-gold-800 shadow-sm hover:shadow-md transition-all duration-200',
        'outline' => 'border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-400 dark:hover:border-gray-500 focus:ring-gray-500 focus:ring-offset-2 active:bg-gray-100 dark:active:bg-gray-600 transition-all duration-200',
        'ghost' => 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:ring-gray-500 focus:ring-offset-2 active:bg-gray-200 dark:active:bg-gray-600 transition-all duration-200',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];

    $variantClass = $variants[$variant] ?? $variants['primary'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<button 
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "inline-flex items-center justify-center font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition-colors disabled:opacity-50 disabled:cursor-not-allowed {$variantClass} {$sizeClass}"]) }}
>
    {{ $slot }}
</button>
