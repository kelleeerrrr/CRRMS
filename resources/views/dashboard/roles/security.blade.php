<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <x-dashboard.stat-card title="Active Reservations" :value="$stats['active_reservations']" icon="calendar" color="blue" />
        <x-dashboard.stat-card title="Check-ins Today" :value="$stats['checkins_today']" icon="check-circle" color="green" />
        <x-dashboard.stat-card title="Facility Access" :value="$stats['facility_access']" icon="home" color="purple" />
        <x-dashboard.stat-card title="Today's Events" :value="$stats['todays_events']" icon="document" color="orange" />
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <x-card title="Recent Reservations">
            @if(empty($recentReservations))
                <x-empty-state title="No recent reservations" description="No recent facility reservations." />
            @else
                <div class="space-y-3">
                    @foreach($recentReservations as $reservation)
                        <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center mr-4">
                                <x-heroicon-o-calendar class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $reservation['facility'] }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $reservation['user'] }} • {{ $reservation['start_time'] }}</p>
                            </div>
                            <x-status-badge :status="$reservation['status']" />
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>

        <x-dashboard.activity-timeline :activities="$recentActivity" title="Recent Activity" />
    </div>

    <!-- Quick Actions -->
    <x-dashboard.quick-actions :actions="[
        ['title' => 'Verify QR Code', 'description' => 'Verify a QR code for check-in', 'icon' => 'check', 'url' => '#'],
        ['title' => 'View Active Reservations', 'description' => 'View all active reservations', 'icon' => 'calendar', 'url' => route('facilities.reservations.index')],
        ['title' => 'View Access Logs', 'description' => 'View facility access logs', 'icon' => 'document', 'url' => route('activity-logs.index')],
        ['title' => 'View Calendar', 'description' => 'View today\'s events', 'icon' => 'calendar', 'url' => route('calendar.index')],
    ]" title="Quick Actions" />
</div>
