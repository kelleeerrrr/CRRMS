<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <x-dashboard.stat-card title="My Reservations" :value="$stats['my_reservations']" icon="calendar" color="blue" />
        <x-dashboard.stat-card title="Borrowed Equipment" :value="$stats['borrowed_equipment']" icon="cube" color="purple" />
        <x-dashboard.stat-card title="Upcoming Reservations" :value="$stats['upcoming_reservations']" icon="clock" color="green" />
        <x-dashboard.stat-card title="Announcements" :value="$stats['announcements']" icon="bell" color="orange" />
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <x-dashboard.line-chart :data="$reservationHistory" title="Reservation History" />
        <x-dashboard.line-chart :data="$borrowHistory" title="Borrow History" />
    </div>

    <!-- Upcoming Tasks -->
    <x-card title="Upcoming Tasks">
        @if(empty($upcomingTasks))
            <x-empty-state title="No upcoming tasks" description="You have no upcoming reservations or returns." />
        @else
            <div class="space-y-3">
                @foreach($upcomingTasks as $task)
                    <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="w-10 h-10 rounded-full {{ $task['type'] === 'reservation' ? 'bg-blue-100 dark:bg-blue-900' : 'bg-purple-100 dark:bg-purple-900' }} flex items-center justify-center mr-4">
                            @if($task['type'] === 'reservation')
                                <x-heroicon-o-calendar class="w-5 h-5 {{ $task['type'] === 'reservation' ? 'text-blue-600 dark:text-blue-400' : 'text-purple-600 dark:text-purple-400' }}" />
                            @else
                                <x-heroicon-o-cube class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $task['title'] }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Due: {{ $task['due_at'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>

    <!-- Latest Announcements -->
    <x-card title="Latest Announcements">
        @if(empty($latestAnnouncements))
            <x-empty-state title="No announcements" description="No recent announcements." />
        @else
            <div class="space-y-3">
                @foreach($latestAnnouncements as $announcement)
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <div class="flex items-start space-x-3">
                            <x-heroicon-o-bell class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $announcement['title'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $announcement['published_at'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>

    <!-- Quick Actions -->
    <x-dashboard.quick-actions :actions="[
        ['title' => 'Reserve Facility', 'description' => 'Book a facility for your class or event', 'icon' => 'plus', 'url' => route('facilities.reservations.create')],
        ['title' => 'Borrow Equipment', 'description' => 'Request equipment for your use', 'icon' => 'cube', 'url' => route('equipment.borrow-requests.create')],
        ['title' => 'View Calendar', 'description' => 'View your reservation calendar', 'icon' => 'calendar', 'url' => route('calendar.index')],
        ['title' => 'My Reservations', 'description' => 'View all your reservations', 'icon' => 'document', 'url' => route('facilities.reservations.index')],
    ]" title="Quick Actions" />
</div>
