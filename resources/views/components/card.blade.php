@props([
    'title' => null,
    'subtitle' => null,
    'actions' => null,
    'padding' => 'p-6',
])

<div {{ $attributes->merge(['class' => 'rounded-2xl shadow-sm overflow-hidden transition-shadow duration-200 hover:shadow-md']) }} style="background: var(--card-bg); border: 1px solid var(--card-border);">
    @if($title || $actions)
        <div class="flex items-center justify-between px-6 py-4 border-b rounded-t-2xl" style="border-color: var(--card-header-border); background: var(--card-header-bg);">
            <div>
                @if($title)
                    <h3 class="text-lg font-semibold" style="color: var(--card-header-text);">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="text-sm mt-0.5" style="color: var(--text-secondary);">{{ $subtitle }}</p>
                @endif
            </div>
            @if($actions)
                <div class="flex items-center space-x-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
</div>
