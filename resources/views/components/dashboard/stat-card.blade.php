@props([
    'title' => null,
    'value' => null,
    'icon' => null,
    'trend' => null,
    'trendUp' => true,
    'color' => 'orange-light',
    'loading' => false,
])

@php
$statColors = [
    'orange-light' => ['bg' => 'var(--stat-orange-light-bg)', 'border' => 'var(--stat-orange-light-border)', 'chip' => 'var(--stat-orange-light-chip)'],
    'orange-deep' => ['bg' => 'var(--stat-orange-deep-bg)', 'border' => 'var(--stat-orange-deep-border)', 'chip' => 'var(--stat-orange-deep-chip)'],
];
$themeColor = $statColors[$color] ?? $statColors['orange-light'];
@endphp

<div class="relative overflow-hidden rounded-2xl p-[18px_20px] shadow-sm" style="background: {{ $themeColor['bg'] }}; border: 1px solid {{ $themeColor['border'] }};">
    @if($loading)
        <x-loading-skeleton class="h-16" />
    @else
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0 pr-2">
                <p class="text-xs" style="color: var(--text-secondary);">{{ $title }}</p>
                <p class="text-[28px] font-bold truncate" style="color: var(--text-primary);">{{ $value }}</p>
                @if($trend !== null)
                    <p class="text-xs mt-1" style="color: {{ $trendUp ? 'var(--badge-approved-text)' : 'var(--badge-rejected-text)' }};">
                        @if($trendUp)
                            <x-heroicon-o-arrow-trend-up class="w-3 h-3 inline mr-1" />
                        @else
                            <x-heroicon-o-arrow-trend-down class="w-3 h-3 inline mr-1" />
                        @endif
                        {{ $trend }}
                    </p>
                @endif
            </div>
            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0" style="background: {{ $themeColor['chip'] }};">
                @if($icon === 'users')
                    <x-heroicon-o-users class="w-5 h-5" style="color: var(--stat-icon-color);" />
                @elseif($icon === 'building')
                    <x-heroicon-o-building-office class="w-5 h-5" style="color: var(--stat-icon-color);" />
                @elseif($icon === 'calendar')
                    <x-heroicon-o-calendar class="w-5 h-5" style="color: var(--stat-icon-color);" />
                @elseif($icon === 'cube')
                    <x-heroicon-o-cube class="w-5 h-5" style="color: var(--stat-icon-color);" />
                @elseif($icon === 'check-circle')
                    <x-heroicon-o-check-circle class="w-5 h-5" style="color: var(--stat-icon-color);" />
                @elseif($icon === 'clock')
                    <x-heroicon-o-clock class="w-5 h-5" style="color: var(--stat-icon-color);" />
                @elseif($icon === 'exclamation')
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5" style="color: var(--stat-icon-color);" />
                @elseif($icon === 'chart')
                    <x-heroicon-o-chart-bar class="w-5 h-5" style="color: var(--stat-icon-color);" />
                @elseif($icon === 'document')
                    <x-heroicon-o-document-text class="w-5 h-5" style="color: var(--stat-icon-color);" />
                @elseif($icon === 'home')
                    <x-heroicon-o-home class="w-5 h-5" style="color: var(--stat-icon-color);" />
                @elseif($icon === 'wrench')
                    <x-heroicon-o-wrench-screwdriver class="w-5 h-5" style="color: var(--stat-icon-color);" />
                @elseif($icon === 'bell')
                    <x-heroicon-o-bell class="w-5 h-5" style="color: var(--stat-icon-color);" />
                @else
                    <x-heroicon-o-chart-bar class="w-5 h-5" style="color: var(--stat-icon-color);" />
                @endif
            </div>
        </div>
    @endif
</div>
