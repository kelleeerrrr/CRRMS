<x-slot name="title">Equipment Reports</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Reports', 'url' => route('reports.index')],
        ['title' => 'Equipment', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Equipment Reports</h1>
        <p class="text-gray-600 dark:text-gray-400">View and export equipment borrowing data</p>
    </div>
</x-slot>

<!-- Filters -->
<x-card class="mb-6">
    <form action="{{ route('reports.equipment') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <x-input
            type="date"
            name="start_date"
            label="Start Date"
            :value="$filters['start_date'] ?? ''"
        />
        
        <x-input
            type="date"
            name="end_date"
            label="End Date"
            :value="$filters['end_date'] ?? ''"
        />
        
        <x-select
            name="status"
            label="Status"
            :options="[
                '' => 'All Status',
                'pending' => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
                'borrowed' => 'Borrowed',
                'returned' => 'Returned',
                'overdue' => 'Overdue'
            ]"
            :value="$filters['status'] ?? ''"
        />
        
        <x-select
            name="user_id"
            label="User"
            :options="['' => 'All Users'] + $users->pluck('name', 'id')->toArray()"
            :value="$filters['user_id'] ?? ''"
        />
        
        <div class="md:col-span-4 flex justify-end space-x-3 mt-4">
            <a href="{{ route('reports.equipment') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Reset
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Apply Filters
            </button>
            <form action="{{ route('reports.export-equipment') }}" method="POST" class="inline">
                @csrf
                @foreach($filters as $key => $value)
                    @if($value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Export Excel
                </button>
            </form>
        </div>
    </form>
</x-card>

<!-- Results Table -->
<x-card>
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            Results ({{ $data->count() }})
        </h3>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Items</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Borrow Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Return Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($data as $borrowRequest)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $borrowRequest->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $borrowRequest->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $borrowRequest->items->count() }} items</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $borrowRequest->borrow_date?->format('M j, Y') ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $borrowRequest->return_date?->format('M j, Y') ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $borrowRequest->status === 'returned' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                {{ ucfirst($borrowRequest->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <x-empty-state title="No equipment borrows found" description="Adjust your filters to see results." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>
