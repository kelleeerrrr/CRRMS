<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <x-dashboard.stat-card title="Total Reservations" :value="$stats['total_reservations']" icon="calendar" color="blue" />
        <x-dashboard.stat-card title="Total Borrowing" :value="$stats['total_borrowing']" icon="cube" color="purple" />
        <x-dashboard.stat-card title="Total Lending" :value="$stats['total_lending']" icon="document" color="green" />
        <x-dashboard.stat-card title="Resource Utilization" :value="$stats['resource_utilization'] . '%'" icon="chart" color="orange" />
        <x-dashboard.stat-card title="Equipment Utilization" :value="$stats['equipment_utilization'] . '%'" icon="cube" color="blue" />
        <x-dashboard.stat-card title="Facility Utilization" :value="$stats['facility_utilization'] . '%'" icon="home" color="green" />
        <x-dashboard.stat-card title="Maintenance Requests" :value="$stats['maintenance_requests']" icon="wrench" color="yellow" />
        <x-dashboard.stat-card title="Service Requests" :value="$stats['service_requests']" icon="bell" color="red" />
    </div>

    <!-- Reservation Overview -->
    <x-card title="Reservation Overview">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Reservation Status</h4>
                <x-dashboard.donut-chart :data="$reservationOverview['by_status']" title="By Status" />
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Monthly Trend</h4>
                <x-dashboard.line-chart :data="$reservationOverview['monthly_trend']" title="Monthly Trend" />
            </div>
        </div>
    </x-card>

    <!-- Borrowing and Lending Overview -->
    <x-card title="Borrowing and Lending Overview">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Equipment Borrowing</h4>
                <x-dashboard.donut-chart :data="$borrowingLendingOverview['equipment_borrowing']" title="By Status" />
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Resource Borrowing</h4>
                <x-dashboard.donut-chart :data="$borrowingLendingOverview['resource_borrowing']" title="By Status" />
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Resource Lending</h4>
                <x-dashboard.donut-chart :data="$borrowingLendingOverview['resource_lending']" title="By Status" />
            </div>
        </div>
    </x-card>

    <!-- Resource Utilization -->
    <x-card title="Resource Utilization">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Most Used Facilities</h4>
                <x-dashboard.bar-chart :data="$resourceUtilization['most_used_facilities']" title="Facilities" />
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Most Borrowed Equipment</h4>
                <x-dashboard.bar-chart :data="$resourceUtilization['most_borrowed_equipment']" title="Equipment" />
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Most Borrowed Resources</h4>
                <x-dashboard.bar-chart :data="$resourceUtilization['most_borrowed_resources']" title="Resources" />
            </div>
        </div>
    </x-card>

    <!-- Maintenance / Service Overview -->
    <x-card title="Maintenance / Service Overview">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Maintenance Requests</h4>
                <x-dashboard.donut-chart :data="$maintenanceServiceOverview['maintenance']" title="By Status" />
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Service Requests</h4>
                <x-dashboard.donut-chart :data="$maintenanceServiceOverview['service']" title="By Status" />
            </div>
        </div>
    </x-card>

    <!-- Recent Activity -->
    <x-card title="Recent System Activity">
        @if(empty($recentActivity))
            <x-empty-state title="No recent activity" description="No recent system activity recorded." />
        @else
            <div class="space-y-3">
                @foreach($recentActivity as $activity)
                    <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center mr-4">
                            <x-heroicon-o-user class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $activity['action'] }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $activity['description'] }} • {{ $activity['user'] }} • {{ $activity['time'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>

    <!-- Quick Actions - Monitoring Only -->
    <x-dashboard.quick-actions :actions="[
        ['title' => 'View Reservations', 'description' => 'View facility reservations', 'icon' => 'calendar', 'url' => route('facilities.reservations.index')],
        ['title' => 'View Equipment', 'description' => 'View equipment inventory', 'icon' => 'cube', 'url' => '#'],
        ['title' => 'View Resources', 'description' => 'View school resources', 'icon' => 'document', 'url' => route('resources.index')],
    ]" title="Quick Actions" />
</div>

