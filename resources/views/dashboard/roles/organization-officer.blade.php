<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <x-dashboard.stat-card title="Organization Reservations" :value="$stats['org_reservations']" icon="calendar" color="blue" />
        <x-dashboard.stat-card title="Pending Requests" :value="$stats['pending_requests']" icon="clock" color="yellow" />
        <x-dashboard.stat-card title="Equipment Borrowed" :value="$stats['equipment_borrowed']" icon="cube" color="purple" />
        <x-dashboard.stat-card title="Upcoming Events" :value="$stats['upcoming_events']" icon="document" color="green" />
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <x-dashboard.line-chart :data="$orgActivity" title="Organization Activity" />
        <x-card title="Recent Reservations">
            @if(empty($recentReservations))
                <x-empty-state title="No recent reservations" description="No recent organization reservations." />
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
    </div>

    <!-- Quick Actions -->
    <x-dashboard.quick-actions :actions="[
        ['title' => 'Reserve Facility', 'description' => 'Book a facility for your organization', 'icon' => 'plus', 'url' => route('facilities.reservations.create')],
        ['title' => 'Borrow Equipment', 'description' => 'Request equipment for your organization', 'icon' => 'cube', 'url' => route('equipment.borrow-requests.create')],
        ['title' => 'View Calendar', 'description' => 'View organization calendar', 'icon' => 'calendar', 'url' => route('calendar.index')],
        ['title' => 'View Reports', 'description' => 'View organization activity reports', 'icon' => 'chart', 'url' => route('reports.index')],
    ]" title="Quick Actions" />
</div>
