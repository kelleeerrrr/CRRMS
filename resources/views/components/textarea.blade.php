@props([
    'name',
    'id' => null,
    'value' => '',
    'label' => null,
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'error' => null,
    'rows' => 3,
])

@php
    $id = $id ?? $name;
@endphp

<div {{ $attributes }}>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-danger-500">*</span>
            @endif
        </label>
    @endif

    <textarea
        name="{{ $name }}"
        id="{{ $id }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $error ? 'aria-invalid="true"' : '' }}
        class="w-full px-3.5 py-2.5 border {{ $error ? 'border-danger-500 focus:ring-danger-500 focus:border-danger-500' : 'border-gray-300 dark:border-gray-600 focus:ring-primary-500 focus:border-primary-500' }} rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed resize-none shadow-sm"
    >{{ old($name, $value) }}</textarea>

    @if($error)
        <p class="mt-1.5 text-sm text-danger-600 dark:text-danger-400">{{ $error }}</p>
    @endif
</div>
