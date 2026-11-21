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
                @endphp
                <div class="flex flex-wrap items-start justify-between gap-3 py-4">
                    <div class="min-w-0">
                        <a href="{{ route($detailRoute, $record) }}" class="font-semibold text-blue-700 hover:underline dark:text-blue-300">{{ $name }}</a>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            {{ $start?->format($category === 'resources' ? 'M j, Y g:i A' : 'M j, Y') }}
                            @if($end)
                                – {{ $end->format($category === 'resources' ? 'M j, Y g:i A' : 'M j, Y') }}
                            @endif
                        </p>
                        <p class="break-all text-xs text-gray-500 dark:text-gray-400">Reference {{ $record->id }}</p>
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