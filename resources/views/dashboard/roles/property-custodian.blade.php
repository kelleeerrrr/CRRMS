<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <x-dashboard.stat-card title="Ready for Release" :value="$stats['ready_release']" icon="check-circle" color="green" />
        <x-dashboard.stat-card title="Due Today" :value="$stats['due_today']" icon="clock" color="yellow" />
        <x-dashboard.stat-card title="Overdue Returns" :value="$stats['overdue']" icon="exclamation" color="red" />
        <x-dashboard.stat-card title="Damaged Equipment" :value="$stats['damaged']" icon="wrench" color="orange" />
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <x-dashboard.bar-chart :data="$borrowFrequency" title="Borrow Frequency" />
        <x-dashboard.donut-chart :data="$equipmentCategories" title="Equipment Categories" />
    </div>

    <!-- Recent Borrow Requests -->
    <x-card title="Recent Borrow Requests">
        @if(empty($recentBorrows))
            <x-empty-state title="No recent borrow requests" description="No recent equipment borrow requests." />
        @else
            <div class="space-y-3">
                @foreach($recentBorrows as $borrow)
                    <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center mr-4">
                            <x-heroicon-o-cube class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $borrow['user'] }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Borrow: {{ $borrow['borrow_date'] }} • Return: {{ $borrow['return_date'] }}</p>
                        </div>
                        <x-status-badge :status="$borrow['status']" />
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>

    <!-- Quick Actions -->
    <x-dashboard.quick-actions :actions="[
        ['title' => 'Release Equipment', 'description' => 'Release approved equipment', 'icon' => 'check', 'url' => route('equipment.borrow-requests.index')],
        ['title' => 'Accept Returns', 'description' => 'Process equipment returns', 'icon' => 'cube', 'url' => route('equipment.borrow-requests.index')],
        ['title' => 'Record Damage', 'description' => 'Report damaged equipment', 'icon' => 'wrench', 'url' => route('equipment.index')],
        ['title' => 'View Inventory', 'description' => 'Check inventory status', 'icon' => 'document', 'url' => route('inventory.index')],
    ]" title="Quick Actions" />
</div>
