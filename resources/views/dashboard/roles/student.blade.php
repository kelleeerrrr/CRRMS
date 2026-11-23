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
            <section class="flex min-w-0 flex-col rounded-xl border" style="background: var(--section-card-bg); border-color: var(--section-card-border);">
                <div class="flex items-center gap-3 border-b px-4 py-3" style="border-color: var(--section-card-border);">
                    <span class="rounded-lg p-2" style="background: var(--section-card-icon-bg);">
                        <x-heroicon-o-users class="h-5 w-5" style="color: var(--section-card-icon-color);" />
                    </span>
                    <h4 class="font-semibold" style="color: var(--text-primary);">User Resources</h4>
                </div>
                <div class="flex flex-1 flex-col gap-3 p-4">
                    @php
                        $userResources = \App\Models\Resource::where('status', 'available')
                            ->with('owner')
                            ->orderBy('created_at', 'desc')
                            ->limit(3)
                            ->get();
                    @endphp
                    @if($userResources->count() > 0)
                        @foreach($userResources as $resource)
                            <div class="rounded-lg border p-3 flex items-start space-x-3" style="background: var(--input-bg); border-color: var(--section-card-border);">
                                @if($resource->photos && !empty($resource->photos))
                                    <img src="{{ asset('storage/' . $resource->photos[0]) }}" alt="{{ $resource->name }}" class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                                @elseif($resource->photo)
                                    <img src="{{ asset('storage/' . $resource->photo) }}" alt="{{ $resource->name }}" class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                                @else
                                    <div class="w-16 h-16 rounded-lg flex items-center justify-center flex-shrink-0" style="background: var(--card-bg);">
                                        <x-heroicon-o-cube class="w-8 h-8" style="color: var(--text-muted);" />
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="break-words text-sm font-semibold" style="color: var(--text-primary);">{{ $resource->name }}</p>
                                    <p class="mt-1 break-words text-xs" style="color: var(--text-secondary);">Owner: {{ $resource->owner?->name ?? 'N/A' }}</p>
                                    <p class="mt-1 break-words text-xs" style="color: var(--text-secondary);">Condition: {{ ucfirst($resource->condition) }}</p>
                                </div>
                            </div>
                        @endforeach
                        <a href="{{ route('resources.index') }}" class="mt-auto self-start pt-1 text-sm font-semibold" style="color: var(--link-color);">View all resources →</a>
                    @else
                        <p class="text-sm" style="color: var(--text-muted);">No user resources available</p>
                    @endif
                </div>
            </section>

            <section class="flex min-w-0 flex-col rounded-xl border" style="background: var(--section-card-bg); border-color: var(--section-card-border);">
                <div class="flex items-center gap-3 border-b px-4 py-3" style="border-color: var(--section-card-border);">
                    <span class="rounded-lg p-2" style="background: var(--section-card-icon-bg);">
                        <x-heroicon-o-building-office class="h-5 w-5" style="color: var(--section-card-icon-color);" />
                    </span>
                    <h4 class="font-semibold" style="color: var(--text-primary);">Available Facilities</h4>
                </div>
                <div class="flex flex-1 flex-col gap-3 p-4">
                    @php
                        $facilities = \App\Models\Facility::where('status', 'available')
                            ->with('room')
                            ->orderBy('created_at', 'desc')
                            ->limit(3)
                            ->get();
                    @endphp
                    @if($facilities->count() > 0)
                        @foreach($facilities as $facility)
                            <div class="rounded-lg border p-3 flex items-start space-x-3" style="background: var(--input-bg); border-color: var(--section-card-border);">
                                @if($facility->photos && !empty($facility->photos))
                                    <img src="{{ asset('storage/' . $facility->photos[0]) }}" alt="{{ $facility->name }}" class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                                @else
                                    <div class="w-16 h-16 rounded-lg flex items-center justify-center flex-shrink-0" style="background: var(--card-bg);">
                                        <x-heroicon-o-building-office class="w-8 h-8" style="color: var(--text-muted);" />
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="break-words text-sm font-semibold" style="color: var(--text-primary);">{{ $facility->name }}</p>
                                    <p class="mt-1 break-words text-xs" style="color: var(--text-secondary);">{{ $facility->room->name ?? 'N/A' }}</p>
                                    <p class="mt-1 break-words text-xs" style="color: var(--text-secondary);">Capacity: {{ $facility->capacity }}</p>
                                </div>
                            </div>
                        @endforeach
                        <a href="{{ route('facilities.reservations.create') }}" class="mt-auto self-start pt-1 text-sm font-semibold" style="color: var(--link-color);">Reserve a facility →</a>
                    @else
                        <p class="text-sm" style="color: var(--text-muted);">No facilities available</p>
                    @endif
                </div>
            </section>

            <section class="flex min-w-0 flex-col rounded-xl border" style="background: var(--section-card-bg); border-color: var(--section-card-border);">
                <div class="flex items-center gap-3 border-b px-4 py-3" style="border-color: var(--section-card-border);">
                    <span class="rounded-lg p-2" style="background: var(--section-card-icon-bg);">
                        <x-heroicon-o-cube class="h-5 w-5" style="color: var(--section-card-icon-color);" />
                    </span>
                    <h4 class="font-semibold" style="color: var(--text-primary);">Available Equipment</h4>
                </div>
                <div class="flex flex-1 flex-col gap-3 p-4">
                    @php
                        $equipment = \App\Models\Equipment::where('status', 'available')
                            ->with('category')
                            ->orderBy('created_at', 'desc')
                            ->limit(3)
                            ->get();
                    @endphp
                    @if($equipment->count() > 0)
                        @foreach($equipment as $item)
                            <div class="rounded-lg border p-3 flex items-start space-x-3" style="background: var(--input-bg); border-color: var(--section-card-border);">
                                @if($item->photos && !empty($item->photos))
                                    <img src="{{ asset('storage/' . $item->photos[0]) }}" alt="{{ $item->name }}" class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                                @elseif($item->equipment_photo)
                                    <img src="{{ asset('storage/' . $item->equipment_photo) }}" alt="{{ $item->name }}" class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                                @else
                                    <div class="w-16 h-16 rounded-lg flex items-center justify-center flex-shrink-0" style="background: var(--card-bg);">
                                        <x-heroicon-o-cube class="w-8 h-8" style="color: var(--text-muted);" />
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="break-words text-sm font-semibold" style="color: var(--text-primary);">{{ $item->name }}</p>
                                    <p class="mt-1 break-words text-xs" style="color: var(--text-secondary);">{{ $item->category->name ?? 'N/A' }}</p>
                                    <p class="mt-1 break-words text-xs" style="color: var(--text-secondary);">Condition: {{ ucfirst($item->condition) }}</p>
                                </div>
                            </div>
                        @endforeach
                        <a href="{{ route('equipment.borrow-requests.create') }}" class="mt-auto self-start pt-1 text-sm font-semibold" style="color: var(--link-color);">Borrow equipment →</a>
                    @else
                        <p class="text-sm" style="color: var(--text-muted);">No equipment available</p>
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
