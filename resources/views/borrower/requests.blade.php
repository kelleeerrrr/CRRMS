<x-app-layout>
    <x-slot name="title">{{ $approved ? 'Approved ' : '' }}{{ $title }}</x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $approved ? 'Approved ' : '' }}{{ $title }}</h1>
    </x-slot>

    <x-card>
        <form method="GET" action="{{ url()->current() }}" class="mb-6 flex flex-wrap items-end gap-3">
            <x-input type="search" name="search" label="Search" :value="$search" placeholder="Name, purpose, or reference" />
            <x-button type="submit">Search</x-button>
            @if($search !== '')
                <a href="{{ url()->current() }}" class="px-3 py-2 text-sm text-gray-600 dark:text-gray-300">Clear</a>
            @endif
            <a href="{{ route($browseRoute) }}" class="px-3 py-2 text-sm font-medium text-blue-600 dark:text-blue-400">Browse {{ $category }}</a>
        </form>

        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($records as $record)
                @php
                    [$name, $start, $end] = match ($category) {
                        'facilities' => [$record->facility?->name ?? $record->title, $record->start_date, $record->end_date],
                        'equipment' => [$record->items->pluck('equipment.name')->filter()->unique()->join(', ') ?: $record->purpose, $record->borrow_date, $record->return_date],
                        'resources' => [$record->resource?->name ?? $record->purpose, $record->start_date, $record->end_date],
                    };
                    $canEdit = !$approved && ($category === 'resources'
                        ? $record->status === 'pending'
                        : auth()->user()->can('update', $record));
                    $canWithdraw = !$approved && ($category === 'resources'
                        ? $record->status === 'pending'
                        : auth()->user()->can('delete', $record));
                @endphp
                <div class="flex flex-wrap items-start justify-between gap-3 py-4">
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $name }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            {{ $start?->format($category === 'resources' ? 'M j, Y g:i A' : 'M j, Y') }}
                            @if($end)
                                – {{ $end->format($category === 'resources' ? 'M j, Y g:i A' : 'M j, Y') }}
                            @endif
                        </p>
                        <p class="break-all text-xs text-gray-500 dark:text-gray-400">Reference {{ $record->id }}</p>
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <a href="{{ route($requestRoute . '.show', [$record, 'from' => $approved ? 'approved' : null]) }}"
                               class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700">
                                View Details
                            </a>
                            @if($canEdit)
                                <a href="{{ route($requestRoute . '.edit', $record) }}"
                                   class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                                    Edit
                                </a>
                            @endif
                            @if($canWithdraw)
                                <form method="POST" action="{{ route($requestRoute . '.destroy', $record) }}" onsubmit="return confirm('Withdraw this request?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-button type="submit" variant="danger" size="sm">Withdraw</x-button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <x-status-badge :status="$record->status" />
                </div>
            @empty
                <p class="py-6 text-sm text-gray-500 dark:text-gray-400">
                    {{ $search !== '' ? 'No matching requests.' : ($approved ? 'No approved requests yet.' : 'No requests yet.') }}
                </p>
            @endforelse
        </div>

        @if($records->hasPages())
            <div class="mt-4">{{ $records->links() }}</div>
        @endif
    </x-card>
</x-app-layout>
