@props([
    'name',
    'id' => null,
    'options' => [],
    'value' => null,
    'label' => null,
    'placeholder' => 'Select an option',
    'required' => false,
    'disabled' => false,
    'error' => null,
])

@php
    $id = $id ?? $name;
@endphp

<div {{ $attributes->whereDoesntStartWith('wire:model') }}>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium mb-1.5" style="color: var(--text-secondary);">
            {{ $label }}
            @if($required)
                <span style="color: var(--badge-rejected-text);">*</span>
            @endif
        </label>
    @endif

    <select
        {{ $attributes->whereStartsWith('wire:model') }}
        name="{{ $name }}"
        id="{{ $id }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $error ? 'aria-invalid="true"' : '' }}
        class="w-full px-3.5 py-2.5 border rounded-lg focus:outline-none focus:ring-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-sm"
        style="background: var(--input-bg); border-color: {{ $error ? 'var(--badge-rejected-text)' : 'var(--input-border)' }}; color: var(--text-primary);"
        @if(!$error)
            onfocus="this.style.borderColor='var(--input-focus-ring)'; this.style.boxShadow='0 0 0 3px var(--input-focus-ring)';"
            onblur="this.style.borderColor='var(--input-border)'; this.style.boxShadow='none';"
        @endif
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $key => $option)
            <option value="{{ $key }}" {{ old($name, $value) == $key ? 'selected' : '' }}>
                {{ is_array($option) ? $option['label'] : $option }}
            </option>
        @endforeach
    </select>

    @if($error)
        <p class="mt-1.5 text-sm" style="color: var(--badge-rejected-text);">{{ $error }}</p>
    @endif
</div>
