<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
        <x-dashboard.stat-card title="Pending Facility Reservations" :value="$stats['pending_facility_reservations']" icon="calendar" color="orange-light" />
        <x-dashboard.stat-card title="Pending Equipment Borrow Request" :value="$stats['pending_equipment_borrows']" icon="cube" color="orange-deep" />
        <x-dashboard.stat-card title="Pending Resource Request" :value="$stats['pending_resource_requests']" icon="document" color="orange-light" />
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('facilities.index') }}" class="flex items-center justify-center p-4 rounded-lg border transition-colors hover:shadow-md" style="background: var(--section-card-bg); border-color: var(--section-card-border);">
            <div class="text-center">
                <x-heroicon-o-calendar class="w-8 h-8 mx-auto mb-2" style="color: var(--section-card-icon-color);" />
                <p class="font-semibold" style="color: var(--text-primary);">View Reservations</p>
                <p class="text-sm" style="color: var(--text-secondary);">View all School Facility</p>
            </div>
        </a>
        <a href="{{ route('equipment.index') }}" class="flex items-center justify-center p-4 rounded-lg border transition-colors hover:shadow-md" style="background: var(--section-card-bg); border-color: var(--section-card-border);">
            <div class="text-center">
                <x-heroicon-o-cube class="w-8 h-8 mx-auto mb-2" style="color: var(--section-card-icon-color);" />
                <p class="font-semibold" style="color: var(--text-primary);">View Equipment</p>
                <p class="text-sm" style="color: var(--text-secondary);">View all School Equipment</p>
            </div>
        </a>
        <a href="{{ route('resources.index') }}" class="flex items-center justify-center p-4 rounded-lg border transition-colors hover:shadow-md" style="background: var(--section-card-bg); border-color: var(--section-card-border);">
            <div class="text-center">
                <x-heroicon-o-document class="w-8 h-8 mx-auto mb-2" style="color: var(--section-card-icon-color);" />
                <p class="font-semibold" style="color: var(--text-primary);">View Resources</p>
                <p class="text-sm" style="color: var(--text-secondary);">View all School Resources</p>
            </div>
        </a>
    </div>

    <!-- Pending Requests Section -->
    <x-card title="Pending Requests">
        <div x-data="{ filter: 'all' }">
            <!-- Filter Buttons -->
            <nav aria-label="Pending request queues" class="flex flex-wrap gap-2 mb-4">
                <button @click="filter = 'all'" :class="filter === 'all' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' : 'bg-white text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700'" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium transition-colors">
                    All
                </button>
                <button @click="filter = 'facility_reservation'" :class="filter === 'facility_reservation' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' : 'bg-white text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700'" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium transition-colors">
                    Facility Reservations
                </button>
                <button @click="filter = 'equipment_borrow'" :class="filter === 'equipment_borrow' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' : 'bg-white text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700'" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium transition-colors">
                    Equipment Requests
                </button>
                <button @click="filter = 'resource_borrow'" :class="filter === 'resource_borrow' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' : 'bg-white text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700'" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium transition-colors">
                    Resource Requests
                </button>
            </nav>

            @if(empty($pendingRequests))
                <div x-show="filter === 'all'">
                    <x-empty-state title="No pending requests" description="All caught up!" />
                </div>
                <div x-show="filter === 'facility_reservation'">
                    <x-empty-state title="No pending facility reservations" description="All caught up!" />
                </div>
                <div x-show="filter === 'equipment_borrow'">
                    <x-empty-state title="No pending equipment requests" description="All caught up!" />
                </div>
                <div x-show="filter === 'resource_borrow'">
                    <x-empty-state title="No pending resource requests" description="All caught up!" />
                </div>
            @else
                <div class="space-y-3">
                    @php
                        $hasFacility = collect($pendingRequests)->contains('type', 'facility_reservation');
                        $hasEquipment = collect($pendingRequests)->contains('type', 'equipment_borrow');
                        $hasResource = collect($pendingRequests)->contains('type', 'resource_borrow');
                    @endphp
                    <div x-show="filter === 'facility_reservation' && {{ $hasFacility ? 'false' : 'true' }}">
                        <x-empty-state title="No pending facility reservations" description="All caught up!" />
                    </div>
                    <div x-show="filter === 'equipment_borrow' && {{ $hasEquipment ? 'false' : 'true' }}">
                        <x-empty-state title="No pending equipment requests" description="All caught up!" />
                    </div>
                    <div x-show="filter === 'resource_borrow' && {{ $hasResource ? 'false' : 'true' }}">
                        <x-empty-state title="No pending resource requests" description="All caught up!" />
                    </div>
                    @foreach($pendingRequests as $request)
                        <div x-show="filter === 'all' || filter === '{{ $request['type'] }}'" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full @if($request['type'] === 'facility_reservation') bg-blue-100 dark:bg-blue-900 @elseif($request['type'] === 'equipment_borrow') bg-purple-100 dark:bg-purple-900 @else bg-green-100 dark:bg-green-900 @endif flex items-center justify-center mr-4">
                                    @if($request['type'] === 'facility_reservation')
                                        <x-heroicon-o-calendar class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                    @elseif($request['type'] === 'equipment_borrow')
                                        <x-heroicon-o-cube class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                    @else
                                        <x-heroicon-o-document class="w-5 h-5 text-green-600 dark:text-green-400" />
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $request['title'] }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $request['user'] }} • {{ $request['date'] }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <x-status-badge :status="$request['status']" />
                                <a href="{{ $request['url'] }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">
                                    <x-heroicon-o-arrow-right class="w-5 h-5" />
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </x-card>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <x-dashboard.bar-chart :data="$charts['facilities']" title="Facility Reservations" />
        <x-dashboard.bar-chart :data="$charts['equipment']" title="Equipment Status" />
        <x-dashboard.bar-chart :data="$charts['resources']" title="Resources Available" />
    </div>
</div>
