@props(['status' => 'pending', 'size' => 'sm'])

@php
    $config = [
        'pending' => ['bg' => 'bg-gold-100', 'text' => 'text-gold-800', 'dark-bg' => 'dark:bg-gold-900/30', 'dark-text' => 'dark:text-gold-400'],
        'approved' => ['bg' => 'bg-success-100', 'text' => 'text-success-800', 'dark-bg' => 'dark:bg-success-900/30', 'dark-text' => 'dark:text-success-400'],
        'rejected' => ['bg' => 'bg-danger-100', 'text' => 'text-danger-800', 'dark-bg' => 'dark:bg-danger-900/30', 'dark-text' => 'dark:text-danger-400'],
        'active' => ['bg' => 'bg-success-100', 'text' => 'text-success-800', 'dark-bg' => 'dark:bg-success-900/30', 'dark-text' => 'dark:text-success-400'],
        'inactive' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'dark-bg' => 'dark:bg-gray-900/30', 'dark-text' => 'dark:text-gray-400'],
        'suspended' => ['bg' => 'bg-danger-100', 'text' => 'text-danger-800', 'dark-bg' => 'dark:bg-danger-900/30', 'dark-text' => 'dark:text-danger-400'],
        'completed' => ['bg' => 'bg-primary-100', 'text' => 'text-primary-800', 'dark-bg' => 'dark:bg-primary-900/30', 'dark-text' => 'dark:text-primary-400'],
        'cancelled' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'dark-bg' => 'dark:bg-gray-900/30', 'dark-text' => 'dark:text-gray-400'],
        'in_progress' => ['bg' => 'bg-primary-100', 'text' => 'text-primary-800', 'dark-bg' => 'dark:bg-primary-900/30', 'dark-text' => 'dark:text-primary-400'],
        'checked_in' => ['bg' => 'bg-success-100', 'text' => 'text-success-800', 'dark-bg' => 'dark:bg-success-900/30', 'dark-text' => 'dark:text-success-400'],
        'checked_out' => ['bg' => 'bg-gold-100', 'text' => 'text-gold-800', 'dark-bg' => 'dark:bg-gold-900/30', 'dark-text' => 'dark:text-gold-400'],
        'overdue' => ['bg' => 'bg-danger-100', 'text' => 'text-danger-800', 'dark-bg' => 'dark:bg-danger-900/30', 'dark-text' => 'dark:text-danger-400'],
        'available' => ['bg' => 'bg-success-100', 'text' => 'text-success-800', 'dark-bg' => 'dark:bg-success-900/30', 'dark-text' => 'dark:text-success-400'],
        'reserved' => ['bg' => 'bg-primary-100', 'text' => 'text-primary-800', 'dark-bg' => 'dark:bg-primary-900/30', 'dark-text' => 'dark:text-primary-400'],
        'borrowed' => ['bg' => 'bg-gold-100', 'text' => 'text-gold-800', 'dark-bg' => 'dark:bg-gold-900/30', 'dark-text' => 'dark:text-gold-400'],
        'returned' => ['bg' => 'bg-success-100', 'text' => 'text-success-800', 'dark-bg' => 'dark:bg-success-900/30', 'dark-text' => 'dark:text-success-400'],
    ];

    $status = strtolower($status);
    $config = $config[$status] ?? $config['pending'];
    $sizeClasses = $size === 'sm' ? 'px-2.5 py-0.5 text-xs' : 'px-3 py-1 text-sm';
@endphp

<span class="inline-flex items-center {{ $sizeClasses }} font-medium rounded-full {{ $config['bg'] }} {{ $config['text'] }} {{ $config['dark-bg'] }} {{ $config['dark-text'] }}">
    {{ ucfirst(str_replace('_', ' ', $status)) }}
</span>
