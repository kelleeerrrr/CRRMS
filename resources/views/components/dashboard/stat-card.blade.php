@props([
    'title' => null,
    'value' => null,
    'icon' => null,
    'trend' => null,
    'trendUp' => true,
    'color' => 'blue',
    'loading' => false,
])

@php
$colors = [
    'blue' => 'bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400',
    'green' => 'bg-success-100 dark:bg-success-900/30 text-success-600 dark:text-success-400',
    'purple' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
    'orange' => 'bg-gold-100 dark:bg-gold-900/30 text-gold-600 dark:text-gold-400',
    'red' => 'bg-danger-100 dark:bg-danger-900/30 text-danger-600 dark:text-danger-400',
    'yellow' => 'bg-gold-100 dark:bg-gold-900/30 text-gold-600 dark:text-gold-400',
];
$iconColor = $colors[$color] ?? $colors['blue'];
@endphp

<x-card class="relative overflow-hidden" padding="p-3">
    @if($loading)
        <x-loading-skeleton class="h-16" />
    @else
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0 pr-2">
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $title }}</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white truncate">{{ $value }}</p>
                @if($trend !== null)
                    <p class="text-xs mt-1 {{ $trendUp ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                        @if($trendUp)
                            <x-heroicon-o-arrow-trend-up class="w-3 h-3 inline mr-1" />
                        @else
                            <x-heroicon-o-arrow-trend-down class="w-3 h-3 inline mr-1" />
                        @endif
                        {{ $trend }}
                    </p>
                @endif
            </div>
            <div class="w-6 h-6 rounded-full {{ $iconColor }} flex items-center justify-center flex-shrink-0">
                @if($icon === 'users')
                    <x-heroicon-o-users class="w-3 h-3" />
                @elseif($icon === 'building')
                    <x-heroicon-o-building-office class="w-3 h-3" />
                @elseif($icon === 'calendar')
                    <x-heroicon-o-calendar class="w-3 h-3" />
                @elseif($icon === 'cube')
                    <x-heroicon-o-cube class="w-3 h-3" />
                @elseif($icon === 'check-circle')
                    <x-heroicon-o-check-circle class="w-3 h-3" />
                @elseif($icon === 'clock')
                    <x-heroicon-o-clock class="w-3 h-3" />
                @elseif($icon === 'exclamation')
                    <x-heroicon-o-exclamation-triangle class="w-3 h-3" />
                @elseif($icon === 'chart')
                    <x-heroicon-o-chart-bar class="w-3 h-3" />
                @elseif($icon === 'document')
                    <x-heroicon-o-document-text class="w-3 h-3" />
                @elseif($icon === 'home')
                    <x-heroicon-o-home class="w-3 h-3" />
                @elseif($icon === 'wrench')
                    <x-heroicon-o-wrench-screwdriver class="w-3 h-3" />
                @elseif($icon === 'bell')
                    <x-heroicon-o-bell class="w-3 h-3" />
                @else
                    <x-heroicon-o-chart-bar class="w-3 h-3" />
                @endif
            </div>
        </div>
    @endif
</x-card>
