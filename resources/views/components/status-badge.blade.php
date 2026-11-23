@props(['status' => 'pending', 'size' => 'sm'])

@php
    $config = [
        'pending' => ['bg' => 'var(--badge-pending-bg)', 'text' => 'var(--badge-pending-text)'],
        'approved' => ['bg' => 'var(--badge-approved-bg)', 'text' => 'var(--badge-approved-text)'],
        'rejected' => ['bg' => 'var(--badge-rejected-bg)', 'text' => 'var(--badge-rejected-text)'],
        'active' => ['bg' => 'var(--badge-approved-bg)', 'text' => 'var(--badge-approved-text)'],
        'inactive' => ['bg' => 'var(--badge-pending-bg)', 'text' => 'var(--badge-pending-text)'],
        'suspended' => ['bg' => 'var(--badge-rejected-bg)', 'text' => 'var(--badge-rejected-text)'],
        'completed' => ['bg' => 'var(--badge-approved-bg)', 'text' => 'var(--badge-approved-text)'],
        'cancelled' => ['bg' => 'var(--badge-pending-bg)', 'text' => 'var(--badge-pending-text)'],
        'in_progress' => ['bg' => 'var(--badge-info-bg)', 'text' => 'var(--badge-info-text)'],
        'checked_in' => ['bg' => 'var(--badge-approved-bg)', 'text' => 'var(--badge-approved-text)'],
        'checked_out' => ['bg' => 'var(--badge-pending-bg)', 'text' => 'var(--badge-pending-text)'],
        'overdue' => ['bg' => 'var(--badge-rejected-bg)', 'text' => 'var(--badge-rejected-text)'],
        'available' => ['bg' => 'var(--badge-approved-bg)', 'text' => 'var(--badge-approved-text)'],
        'reserved' => ['bg' => 'var(--badge-info-bg)', 'text' => 'var(--badge-info-text)'],
        'borrowed' => ['bg' => 'var(--badge-info-bg)', 'text' => 'var(--badge-info-text)'],
        'returned' => ['bg' => 'var(--badge-approved-bg)', 'text' => 'var(--badge-approved-text)'],
        'maintenance' => ['bg' => 'var(--badge-rejected-bg)', 'text' => 'var(--badge-rejected-text)'],
        'unavailable' => ['bg' => 'var(--badge-rejected-bg)', 'text' => 'var(--badge-rejected-text)'],
    ];

    $status = strtolower($status);
    $config = $config[$status] ?? $config['pending'];
    $sizeClasses = $size === 'sm' ? 'px-2.5 py-0.5 text-xs' : 'px-3 py-1 text-sm';
@endphp

<span class="inline-flex items-center {{ $sizeClasses }} font-medium rounded-full" style="background: {{ $config['bg'] }}; color: {{ $config['text'] }};">
    {{ ucfirst(str_replace('_', ' ', $status)) }}
</span>
