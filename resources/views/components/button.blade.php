@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
])

@php
    $sizes = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];
    
    $variantStyles = [
        'primary' => 'background: var(--brand-maroon); color: white; border: none;',
        'secondary' => 'background: white; color: var(--brand-maroon); border: 1px solid var(--brand-maroon);',
        'success' => 'background: var(--badge-approved-text); color: white; border: none;',
        'danger' => 'background: var(--badge-rejected-text); color: white; border: none;',
        'warning' => 'background: var(--badge-pending-text); color: white; border: none;',
        'outline' => 'background: transparent; border: 2px solid var(--brand-maroon); color: var(--brand-maroon);',
        'ghost' => 'background: transparent; color: var(--brand-maroon); border: none;',
    ];
    
    $variantStyle = $variantStyles[$variant] ?? $variantStyles['primary'];
    
    $hoverStyles = [
        'primary' => 'background: var(--brand-maroon-hover);',
        'secondary' => 'background: var(--card-bg);',
        'outline' => 'background: var(--brand-maroon); color: white;',
        'ghost' => 'background: var(--card-bg);',
    ];
    $hoverStyle = $hoverStyles[$variant] ?? '';
@endphp

<button 
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "inline-flex items-center justify-center font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-sm hover:shadow-md {$sizeClass}"]) }}
    style="{{ $variantStyle }}"
    @if(isset($hoverStyles[$variant]))
        onmouseover="this.style='{{ $variantStyle }} {{ $hoverStyle }}'"
        onmouseout="this.style='{{ $variantStyle }}'"
    @endif
>
    {{ $slot }}
</button>
