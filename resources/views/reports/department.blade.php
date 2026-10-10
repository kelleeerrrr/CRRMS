<x-slot name="title">Department Reports</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Reports', 'url' => route('reports.index')],
        ['title' => 'Department', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Department Reports</h1>
        <p class="text-gray-600 dark:text-gray-400">View user distribution and activity by department</p>
    </div>
</x-slot>

<!-- Filters -->
<x-card class="mb-6">
    <form action="{{ route('reports.department') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-select
            name="department_id"
            label="Department"
            :options="['' => 'All Departments'] + $departments->pluck('name', 'id')->toArray()"
            :value="$filters['department_id'] ?? ''"
        />
        
        <div class="md:col-span-2 flex justify-end space-x-3 mt-4">
            <a href="{{ route('reports.department') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Reset
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Apply Filters
            </button>
            <form action="{{ route('reports.export-department') }}" method="POST" class="inline">
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
    <!-- Users by Department -->
    <x-card title="Users by Department">
        <div class="space-y-4">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Department</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Users</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($data['users'] as $stat)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $stat->department->name }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $stat->total_users }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No data available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-card>
    
    <!-- Reservations by Department -->
    <x-card title="Reservations by Department">
        <div class="space-y-4">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Department</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Reservations</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($data['reservations'] as $stat)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $stat->department->name }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $stat->total_reservations }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
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
