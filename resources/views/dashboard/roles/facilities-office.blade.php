<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <x-dashboard.stat-card title="Today's Reservations" :value="$stats['today_reservations']" icon="calendar" color="blue" />
        <x-dashboard.stat-card title="Building Occupancy" :value="$stats['building_occupancy']" icon="building" color="green" />
        <x-dashboard.stat-card title="Room Utilization" :value="$stats['room_utilization']" icon="home" color="purple" />
        <x-dashboard.stat-card title="Upcoming Events" :value="$stats['upcoming_events']" icon="document" color="orange" />
        <x-dashboard.stat-card title="Facility Maintenance" :value="$stats['facility_maintenance']" icon="wrench" color="red" />
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <x-dashboard.bar-chart :data="$facilityUsage" title="Facility Usage" />
        <x-dashboard.line-chart :data="$reservationTrends" title="Reservation Trends" />
    </div>

    <!-- Pending Approvals -->
    <x-card title="Pending Approvals">
        @if(empty($pendingApprovals))
            <x-empty-state title="No pending approvals" description="All caught up!" />
        @else
            <div class="space-y-3">
                @foreach($pendingApprovals as $approval)
                    <a href="{{ $approval['url'] }}" class="flex items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center mr-4">
                            @if($approval['type'] === 'reservation')
                                <x-heroicon-o-calendar class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                            @else
                                <x-heroicon-o-cube class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $approval['title'] }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $approval['user'] }} • {{ $approval['created_at'] }}</p>
                        </div>
                        <x-heroicon-o-chevron-right class="w-5 h-5 text-gray-400" />
                    </a>
                @endforeach
            </div>
        @endif
    </x-card>

    <!-- Recent Reservations -->
    <x-card title="Recent Reservations">
        @if(empty($recentReservations))
            <x-empty-state title="No recent reservations" description="No recent facility reservations." />
        @else
            <div class="space-y-3">
                @foreach($recentReservations as $reservation)
                    <a href="{{ route('facilities.reservations.show', $reservation['id'] ?? '#') }}" class="flex items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center mr-4">
                            <x-heroicon-o-calendar class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $reservation['facility'] }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $reservation['user'] }} • {{ $reservation['start_time'] }}</p>
                        </div>
                        <x-status-badge :status="$reservation['status']" />
                    </a>
                @endforeach
            </div>
        @endif
    </x-card>

    <!-- Quick Actions -->
    <x-dashboard.quick-actions :actions="[
        ['title' => 'Approve Reservations', 'description' => 'Review and approve pending reservations', 'icon' => 'check', 'url' => route('facilities.reservations.index')],
        ['title' => 'Manage Facilities', 'description' => 'View and manage facilities', 'icon' => 'home', 'url' => route('buildings.index')],
        ['title' => 'Maintenance Schedule', 'description' => 'View maintenance schedule', 'icon' => 'wrench', 'url' => route('maintenance.index')],
        ['title' => 'View Calendar', 'description' => 'View facility calendar', 'icon' => 'calendar', 'url' => route('calendar.index')],
    ]" title="Quick Actions" />
</div>
