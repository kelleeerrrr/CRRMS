<x-slot name="title">Reports</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Reports', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reports</h1>
        <p class="text-gray-600 dark:text-gray-400">Generate and export reports</p>
    </div>
</x-slot>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Reservations</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['total_reservations'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                <x-heroicon-o-calendar class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            </div>
        </div>
    </x-card>
    
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Active Reservations</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['active_reservations'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                <x-heroicon-o-check-circle class="w-6 h-6 text-green-600 dark:text-green-400" />
            </div>
        </div>
    </x-card>
    
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Borrows</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['total_borrows'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                <x-heroicon-o-cube class="w-6 h-6 text-purple-600 dark:text-purple-400" />
            </div>
        </div>
    </x-card>
    
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Low Stock Items</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['low_stock_items'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-red-600 dark:text-red-400" />
            </div>
        </div>
    </x-card>
</div>

<!-- Report Types -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <x-card>
        <div class="flex items-start space-x-4">
            <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900 flex items-center justify-center flex-shrink-0">
                <x-heroicon-o-calendar class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Reservation Reports</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">View and export facility reservation data with filters for date, status, user, and facility.</p>
                <a href="{{ route('reports.reservations') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    View Report
                    <x-heroicon-o-arrow-right class="w-4 h-4 ml-2" />
                </a>
            </div>
        </div>
    </x-card>
    
    <x-card>
        <div class="flex items-start space-x-4">
            <div class="w-12 h-12 rounded-lg bg-purple-100 dark:bg-purple-900 flex items-center justify-center flex-shrink-0">
                <x-heroicon-o-cube class="w-6 h-6 text-purple-600 dark:text-purple-400" />
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Equipment Reports</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Track equipment borrowing requests with status and user information.</p>
                <a href="{{ route('reports.equipment') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    View Report
                    <x-heroicon-o-arrow-right class="w-4 h-4 ml-2" />
                </a>
            </div>
        </div>
    </x-card>
    
    <x-card>
        <div class="flex items-start space-x-4">
            <div class="w-12 h-12 rounded-lg bg-green-100 dark:bg-green-900 flex items-center justify-center flex-shrink-0">
                <x-heroicon-o-box class="w-6 h-6 text-green-600 dark:text-green-400" />
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Inventory Reports</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Monitor inventory levels, low stock alerts, and location-based data.</p>
                <a href="{{ route('reports.inventory') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    View Report
                    <x-heroicon-o-arrow-right class="w-4 h-4 ml-2" />
                </a>
            </div>
        </div>
    </x-card>
    
    <x-card>
        <div class="flex items-start space-x-4">
            <div class="w-12 h-12 rounded-lg bg-orange-100 dark:bg-orange-900 flex items-center justify-center flex-shrink-0">
                <x-heroicon-o-chart-bar class="w-6 h-6 text-orange-600 dark:text-orange-400" />
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Usage Reports</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Analyze facility and equipment usage patterns over time periods.</p>
                <a href="{{ route('reports.usage') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                    View Report
                    <x-heroicon-o-arrow-right class="w-4 h-4 ml-2" />
                </a>
            </div>
        </div>
    </x-card>
    
    <x-card>
        <div class="flex items-start space-x-4">
            <div class="w-12 h-12 rounded-lg bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center flex-shrink-0">
                <x-heroicon-o-users class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Department Reports</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">View user distribution and reservation activity by department.</p>
                <a href="{{ route('reports.department') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    View Report
                    <x-heroicon-o-arrow-right class="w-4 h-4 ml-2" />
                </a>
            </div>
        </div>
    </x-card>
</div>
