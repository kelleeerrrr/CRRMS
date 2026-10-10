<x-slot name="title">Usage Reports</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Reports', 'url' => route('reports.index')],
        ['title' => 'Usage', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Usage Reports</h1>
        <p class="text-gray-600 dark:text-gray-400">Analyze facility and equipment usage patterns</p>
    </div>
</x-slot>

<!-- Filters -->
<x-card class="mb-6">
    <form action="{{ route('reports.usage') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-input
            type="date"
            name="start_date"
            label="Start Date"
            :value="$filters['start_date'] ?? now()->subMonth()->format('Y-m-d')"
        />
        
        <x-input
            type="date"
            name="end_date"
            label="End Date"
            :value="$filters['end_date'] ?? now()->format('Y-m-d')"
        />
        
        <div class="md:col-span-2 flex justify-end space-x-3 mt-4">
            <a href="{{ route('reports.usage') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Reset
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Apply Filters
            </button>
            <form action="{{ route('reports.export-usage') }}" method="POST" class="inline">
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

<!-- Results -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Facility Usage -->
    <x-card title="Facility Usage">
        <div class="space-y-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Period: {{ $data['period']['start']->format('M j, Y') }} - {{ $data['period']['end']->format('M j, Y') }}
            </p>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Facility</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Approved</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($data['reservations'] as $stat)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $stat->facility->name }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $stat->total_reservations }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $stat->approved }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No data available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-card>
    
    <!-- Equipment Usage -->
    <x-card title="Equipment Usage">
        <div class="space-y-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Period: {{ $data['period']['start']->format('M j, Y') }} - {{ $data['period']['end']->format('M j, Y') }}
            </p>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Equipment</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Returned</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($data['equipment_borrows'] as $stat)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $stat->equipment->name }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $stat->total_borrows }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $stat->returned }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No data available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-card>
</div>
