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
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Available Resources</h3>
                <div class="space-y-3">
                    @php
                        $userResources = \App\Models\Resource::where('status', 'available')
                            ->with('owner')
                            ->orderBy('created_at', 'desc')
                            ->limit(3)
                            ->get();
                    @endphp
                    @if($userResources->count() > 0)
                        @foreach($userResources as $resource)
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg flex items-start space-x-3">
                                @if($resource->photos && !empty($resource->photos))
                                    <img src="{{ asset('storage/' . $resource->photos[0]) }}" alt="{{ $resource->name }}" class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                                @elseif($resource->photo)
                                    <img src="{{ asset('storage/' . $resource->photo) }}" alt="{{ $resource->name }}" class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <x-heroicon-o-cube class="w-8 h-8 text-gray-400" />
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 dark:text-white truncate">{{ $resource->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $resource->category ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Condition: {{ ucfirst($resource->condition) }}</p>
                                </div>
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
                            ->orderBy('created_at', 'desc')
                            ->limit(3)
                            ->get();
                    @endphp
                    @if($facilities->count() > 0)
                        @foreach($facilities as $facility)
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg flex items-start space-x-3">
                                @if($facility->photos && !empty($facility->photos))
                                    <img src="{{ asset('storage/' . $facility->photos[0]) }}" alt="{{ $facility->name }}" class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <x-heroicon-o-building-office class="w-8 h-8 text-gray-400" />
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 dark:text-white truncate">{{ $facility->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($facility->type) }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Capacity: {{ $facility->capacity }}</p>
                                </div>
                            </div>
                        @endforeach
                        <a href="{{ route('facilities.index') }}" class="text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400">Reserve a facility →</a>
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
                            ->orderBy('created_at', 'desc')
                            ->limit(3)
                            ->get();
                    @endphp
                    @if($equipment->count() > 0)
                        @foreach($equipment as $item)
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg flex items-start space-x-3">
                                @if($item->photos && !empty($item->photos))
                                    <img src="{{ asset('storage/' . $item->photos[0]) }}" alt="{{ $item->name }}" class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                                @elseif($item->equipment_photo)
                                    <img src="{{ asset('storage/' . $item->equipment_photo) }}" alt="{{ $item->name }}" class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <x-heroicon-o-cube class="w-8 h-8 text-gray-400" />
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 dark:text-white truncate">{{ $item->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->category->name ?? $item->equipment_category ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Condition: {{ ucfirst($item->condition) }}</p>
                                </div>
                            </div>
                        @endforeach
                        <a href="{{ route('equipment.index') }}" class="text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400">Borrow equipment →</a>
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
        <x-dashboard.activity-timeline
            :activities="array_map(fn ($announcement) => [
                'description' => $announcement['title'],
                'user' => $announcement['author'],
                'time' => $announcement['published_at'],
            ], $latestAnnouncements)"
            title="Latest Announcements"
        />
    </div>
</div>
