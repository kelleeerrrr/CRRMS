<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <x-dashboard.stat-card title="Upcoming Reservations" :value="$stats['upcoming_reservations']" icon="calendar" color="blue" />
        <x-dashboard.stat-card title="Borrowed Equipment" :value="$stats['borrowed_equipment']" icon="cube" color="purple" />
        <x-dashboard.stat-card title="Reservation History" :value="$stats['reservation_history']" icon="document" color="green" />
        <x-dashboard.stat-card title="Borrow History" :value="$stats['borrow_history']" icon="clock" color="orange" />
    </div>

    <!-- Available Resources Section -->
    <x-card title="Available to Borrow">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- User Resources -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">User Resources</h3>
                <div class="space-y-3">
                    @php
                        $userResources = \App\Models\Resource::where('owner_type', 'user')
                            ->where('status', 'available')
                            ->with('owner')
                            ->limit(3)
                            ->get();
                    @endphp
                    @if($userResources->count() > 0)
                        @foreach($userResources as $resource)
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $resource->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Owner: {{ $resource->owner->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Condition: {{ ucfirst($resource->condition) }}</p>
                            </div>
                        @endforeach
                        <a href="{{ route('resources.index') }}" class="text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400">View all resources →</a>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No user resources available</p>
                    @endif
                </div>
            </div>

            <!-- Facilities -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Available Facilities</h3>
                <div class="space-y-3">
                    @php
                        $facilities = \App\Models\Facility::where('status', 'available')
                            ->with('room')
                            ->limit(3)
                            ->get();
                    @endphp
                    @if($facilities->count() > 0)
                        @foreach($facilities as $facility)
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $facility->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $facility->room->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Capacity: {{ $facility->capacity }}</p>
                            </div>
                        @endforeach
                        <a href="{{ route('facilities.reservations.create') }}" class="text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400">Reserve a facility →</a>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No facilities available</p>
                    @endif
                </div>
            </div>

            <!-- Equipment -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Available Equipment</h3>
                <div class="space-y-3">
                    @php
                        $equipment = \App\Models\Equipment::where('status', 'available')
                            ->with('category')
                            ->limit(3)
                            ->get();
                    @endphp
                    @if($equipment->count() > 0)
                        @foreach($equipment as $item)
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $item->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->category->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Condition: {{ ucfirst($item->condition) }}</p>
                            </div>
                        @endforeach
                        <a href="{{ route('equipment.borrow-requests.create') }}" class="text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400">Borrow equipment →</a>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No equipment available</p>
                    @endif
                </div>
            </div>
        </div>
    </x-card>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <x-dashboard.line-chart :data="$monthlyActivity" title="Monthly Activity" />
        <x-dashboard.activity-timeline :activities="$latestAnnouncements" title="Latest Announcements" />
    </div>
</div>
