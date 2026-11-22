<x-app-layout>
    <x-slot name="title">{{ $loans ? 'Borrowing Records' : 'Transaction History' }}</x-slot>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $loans ? 'Borrowing Records' : 'Transaction History' }}</h1>
    </x-slot>

    <x-card class="mb-6">
        <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-end gap-3">
            <x-input type="search" name="search" label="Search" :value="$search" maxlength="255" placeholder="Name, purpose, or reference" />
            <x-select name="category" label="Category" :options="array_map(fn ($category) => $category[0], $categories)" :value="$categoryFilter" placeholder="All categories" />
            @unless($loans)
                <x-select name="status" label="Status" :value="$status" :options="[
                    'pending' => 'Pending', 'approved' => 'Approved', 'reserved' => 'Reserved',
                    'checked_in' => 'Checked In', 'in_use' => 'In Use', 'returned' => 'Returned',
                    'completed' => 'Completed', 'rejected' => 'Rejected', 'cancelled' => 'Cancelled',
                ]" placeholder="All statuses" />
            @endunless
            <x-button type="submit">Search</x-button>
            @if($search !== '' || $categoryFilter !== '' || $status !== '')
                <a href="{{ url()->current() }}" class="px-3 py-2 text-sm text-blue-700 dark:text-blue-300">Clear</a>
            @endif
        </form>
    </x-card>

    <x-card>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($records as ['category' => $category, 'label' => $label, 'requestRoute' => $requestRoute, 'record' => $record])
                @php
                    $name = match ($category) {
                        'facilities' => $record->facility?->name ?? $record->title,
                        'equipment' => $record->items->pluck('equipment.name')->filter()->unique()->join(', ') ?: $record->purpose,
                        'resources' => $record->resource?->name ?? $record->purpose,
                    };
                    $releasedAt = $category === 'equipment' ? $record->released_at : $record->checked_out_at;
                    $returnedAt = $record->returned_at ?? $record->actual_return_date ?? null;
                    $due = $category === 'equipment' ? $record->return_date?->copy()->endOfDay() : $record->end_date;
                    $dateFormat = $category === 'equipment' ? 'M j, Y' : 'M j, Y g:i A';
                    $outstanding = $loans && $record->status === 'in_use';
                @endphp
                <article class="py-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h2 class="font-semibold text-gray-900 dark:text-white">{{ $name }}</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $label }}</p>
                        </div>
                        <x-status-badge :status="$record->status" />
                    </div>

                    @if($loans)
                        <dl class="mt-3 grid gap-3 text-sm text-gray-700 dark:text-gray-200 sm:grid-cols-3">
                            <div><dt class="font-medium">Released</dt><dd>{{ $releasedAt?->format('M j, Y g:i A') ?? 'Date unavailable' }}</dd></div>
                            <div><dt class="font-medium">Due</dt><dd>{{ $due?->format($dateFormat) ?? 'No recorded due date' }}</dd></div>
                            <div><dt class="font-medium">Returned</dt><dd>{{ $returnedAt?->format('M j, Y g:i A') ?? ($outstanding ? 'Outstanding' : 'Date unavailable') }}</dd></div>
                        </dl>
                        @if($outstanding && $due?->isPast())
                            <p class="mt-2 text-sm font-medium text-red-700 dark:text-red-300">Overdue</p>
                        @endif
                        <ul class="mt-2 space-y-1 text-sm text-gray-700 dark:text-gray-200">
                            @if($category === 'equipment')
                                @foreach($record->items as $item)
                                    <li>{{ $item->equipment?->name ?? 'Equipment' }} · {{ $item->quantity }} released · {{ $item->returned_quantity ?? 0 }} returned · {{ max(0, $item->quantity - ($item->returned_quantity ?? 0)) }} outstanding</li>
                                @endforeach
                            @else
                                <li>{{ $record->quantity }} released · {{ $outstanding ? $record->quantity . ' outstanding' : ($returnedAt || $record->status === 'returned' ? $record->quantity . ' returned' : 'return quantity unavailable') }}</li>
                            @endif
                        </ul>
                    @else
                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-200">
                            Submitted {{ $record->created_at?->format('M j, Y') }}
                            @if($record->cancelled_at) · Cancelled {{ $record->cancelled_at->format('M j, Y') }} @endif
                            @if($returnedAt) · Returned {{ $returnedAt->format('M j, Y') }} @endif
                        </p>
                    @endif

                    <p class="mt-2 break-all text-xs text-gray-500 dark:text-gray-400">Reference {{ $record->id }}</p>
                    <a href="{{ route($requestRoute . '.show', $record) }}" class="mt-3 inline-flex rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700">View Details</a>
                </article>
            @empty
                <p class="py-6 text-sm text-gray-500 dark:text-gray-400">No matching {{ $loans ? 'borrowing records' : 'transactions' }}.</p>
            @endforelse
        </div>

        <div class="mt-4">{{ $records->links() }}</div>
    </x-card>
</x-app-layout>
