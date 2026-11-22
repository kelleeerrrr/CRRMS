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
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <section class="flex min-w-0 flex-col rounded-xl border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900/40">
                <div class="flex items-center gap-3 border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                    <span class="rounded-lg bg-blue-100 p-2 text-blue-700 dark:bg-blue-900 dark:text-blue-200">
                        <x-heroicon-o-users class="h-5 w-5" />
                    </span>
                    <h4 class="font-semibold text-gray-900 dark:text-white">User Resources</h4>
                </div>
                <div class="flex flex-1 flex-col gap-3 p-4">
                    @php
                        $userResources = \App\Models\Resource::where('owner_type', 'user')
                            ->where('status', 'available')
                            ->with('owner')
                            ->limit(3)
                            ->get();
                    @endphp
                    @if($userResources->count() > 0)
                        @foreach($userResources as $resource)
                            <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800">
                                <p class="break-words text-sm font-semibold text-gray-900 dark:text-white">{{ $resource->name }}</p>
                                <p class="mt-1 break-words text-xs text-gray-500 dark:text-gray-400">Owner: {{ $resource->owner?->name ?? 'N/A' }}</p>
                                <p class="mt-1 break-words text-xs text-gray-500 dark:text-gray-400">Condition: {{ ucfirst($resource->condition) }}</p>
                            </div>
                        @endforeach
                        <a href="{{ route('resources.index') }}" class="mt-auto self-start pt-1 text-sm font-semibold text-blue-700 hover:underline dark:text-blue-300">View all resources →</a>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No user resources available</p>
                    @endif
                </div>
            </section>

            <section class="flex min-w-0 flex-col rounded-xl border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900/40">
                <div class="flex items-center gap-3 border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                    <span class="rounded-lg bg-green-100 p-2 text-green-700 dark:bg-green-900 dark:text-green-200">
                        <x-heroicon-o-building-office class="h-5 w-5" />
                    </span>
                    <h4 class="font-semibold text-gray-900 dark:text-white">Available Facilities</h4>
                </div>
                <div class="flex flex-1 flex-col gap-3 p-4">
                    @php
                        $facilities = \App\Models\Facility::where('status', 'available')
                            ->with('room')
                            ->limit(3)
                            ->get();
                    @endphp
                    @if($facilities->count() > 0)
                        @foreach($facilities as $facility)
                            <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800">
                                <p class="break-words text-sm font-semibold text-gray-900 dark:text-white">{{ $facility->name }}</p>
                                <p class="mt-1 break-words text-xs text-gray-500 dark:text-gray-400">{{ $facility->room->name ?? 'N/A' }}</p>
                                <p class="mt-1 break-words text-xs text-gray-500 dark:text-gray-400">Capacity: {{ $facility->capacity }}</p>
                            </div>
                        @endforeach
                        <a href="{{ route('facilities.reservations.create') }}" class="mt-auto self-start pt-1 text-sm font-semibold text-blue-700 hover:underline dark:text-blue-300">Reserve a facility →</a>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No facilities available</p>
                    @endif
                </div>
            </section>

            <section class="flex min-w-0 flex-col rounded-xl border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900/40">
                <div class="flex items-center gap-3 border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                    <span class="rounded-lg bg-purple-100 p-2 text-purple-700 dark:bg-purple-900 dark:text-purple-200">
                        <x-heroicon-o-cube class="h-5 w-5" />
                    </span>
                    <h4 class="font-semibold text-gray-900 dark:text-white">Available Equipment</h4>
                </div>
                <div class="flex flex-1 flex-col gap-3 p-4">
                    @php
                        $equipment = \App\Models\Equipment::where('status', 'available')
                            ->with('category')
                            ->limit(3)
                            ->get();
                    @endphp
                    @if($equipment->count() > 0)
                        @foreach($equipment as $item)
                            <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800">
                                <p class="break-words text-sm font-semibold text-gray-900 dark:text-white">{{ $item->name }}</p>
                                <p class="mt-1 break-words text-xs text-gray-500 dark:text-gray-400">{{ $item->category->name ?? 'N/A' }}</p>
                                <p class="mt-1 break-words text-xs text-gray-500 dark:text-gray-400">Condition: {{ ucfirst($item->condition) }}</p>
                            </div>
                        @endforeach
                        <a href="{{ route('equipment.borrow-requests.create') }}" class="mt-auto self-start pt-1 text-sm font-semibold text-blue-700 hover:underline dark:text-blue-300">Borrow equipment →</a>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No equipment available</p>
                    @endif
                </div>
            </section>
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
