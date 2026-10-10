<x-slot name="title">Inventory Reports</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Reports', 'url' => route('reports.index')],
        ['title' => 'Inventory', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Inventory Reports</h1>
        <p class="text-gray-600 dark:text-gray-400">View and export inventory data</p>
    </div>
</x-slot>

<!-- Filters -->
<x-card class="mb-6">
    <form action="{{ route('reports.inventory') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-select
            name="category"
            label="Category"
            :options="[
                '' => 'All Categories',
                'electronics' => 'Electronics',
                'furniture' => 'Furniture',
                'supplies' => 'Supplies',
                'equipment' => 'Equipment'
            ]"
            :value="$filters['category'] ?? ''"
        />
        
        <x-select
            name="location_id"
            label="Location"
            :options="['' => 'All Locations'] + $locations->pluck('name', 'id')->toArray()"
            :value="$filters['location_id'] ?? ''"
        />
        
        <label class="flex items-center mt-6">
            <input type="checkbox" name="low_stock" value="1" {{ isset($filters['low_stock']) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Low Stock Only</span>
        </label>
        
        <div class="md:col-span-3 flex justify-end space-x-3 mt-4">
            <a href="{{ route('reports.inventory') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Reset
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Apply Filters
            </button>
            <form action="{{ route('reports.export-inventory') }}" method="POST" class="inline">
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Min Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($data as $inventory)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $inventory->item->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ ucfirst($inventory->item->category) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $inventory->location->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $inventory->quantity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $inventory->min_stock }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($inventory->quantity <= $inventory->min_stock)
                                <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-100">Low Stock</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-100">In Stock</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <x-empty-state title="No inventory found" description="Adjust your filters to see results." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>
