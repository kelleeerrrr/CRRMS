<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <x-dashboard.stat-card title="Pending Facility Reservations" :value="$stats['pending_facility_reservations']" icon="calendar" color="blue" />
        <x-dashboard.stat-card title="Pending Equipment Borrows" :value="$stats['pending_equipment_borrows']" icon="cube" color="purple" />
        <x-dashboard.stat-card title="Pending Resource Requests" :value="$stats['pending_resource_requests']" icon="document" color="green" />
        <x-dashboard.stat-card title="Maintenance Requests" :value="$stats['maintenance_requests']" icon="wrench" color="orange" />
        <x-dashboard.stat-card title="Available Facilities" :value="$stats['available_facilities']" icon="home" color="green" />
        <x-dashboard.stat-card title="Reserved Facilities" :value="$stats['reserved_facilities']" icon="calendar" color="yellow" />
        <x-dashboard.stat-card title="Available Equipment" :value="$stats['available_equipment']" icon="cube" color="blue" />
        <x-dashboard.stat-card title="Borrowed Equipment" :value="$stats['borrowed_equipment']" icon="cube" color="purple" />
    </div>

    <!-- Pending Requests Section -->
    <x-card title="Pending Requests">
        @if(empty($pendingRequests))
            <x-empty-state title="No pending requests" description="All caught up!" />
        @else
            <div class="space-y-3">
                @foreach($pendingRequests as $request)
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
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
    </x-card>

    <!-- Maintenance Section -->
    <x-card title="Maintenance Requests">
        @if(empty($maintenance))
            <x-empty-state title="No maintenance requests" description="No pending or in-progress maintenance." />
        @else
            <div class="space-y-3">
                @foreach($maintenance as $m)
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-900 flex items-center justify-center mr-4">
                                <x-heroicon-o-wrench class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ ucfirst($m['type']) }} Maintenance</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $m['description'] }} • {{ $m['assigned_to'] }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <x-status-badge :status="$m['status']" />
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $m['created_at'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>

    <!-- Resource/Equipment Status -->
    <x-card title="Resource / Equipment Status">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Available Facilities</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $resourceStatus['available_facilities'] }}</p>
                    </div>
                    <x-heroicon-o-home class="w-8 h-8 text-green-600 dark:text-green-400" />
                </div>
            </div>
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Available Equipment</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $resourceStatus['available_equipment'] }}</p>
                    </div>
                    <x-heroicon-o-cube class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
            <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Borrowed Equipment</p>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $resourceStatus['borrowed_equipment'] ?? 0 }}</p>
                    </div>
                    <x-heroicon-o-arrow-right class="w-8 h-8 text-purple-600 dark:text-purple-400" />
                </div>
            </div>
            <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Reserved Facilities</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $resourceStatus['reserved_facilities'] }}</p>
                    </div>
                    <x-heroicon-o-calendar class="w-8 h-8 text-yellow-600 dark:text-yellow-400" />
                </div>
            </div>
            <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Under Maintenance</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $resourceStatus['under_maintenance'] }}</p>
                    </div>
                    <x-heroicon-o-wrench class="w-8 h-8 text-red-600 dark:text-red-400" />
                </div>
            </div>
        </div>
    </x-card>

    <!-- Quick Actions -->
    <x-dashboard.quick-actions :actions="[
        ['title' => 'View Reservations', 'description' => 'View all facility reservations', 'icon' => 'calendar', 'url' => route('facilities.reservations.index')],
        ['title' => 'View Equipment', 'description' => 'View equipment inventory', 'icon' => 'cube', 'url' => '#'],
        ['title' => 'View Resources', 'description' => 'View school resources', 'icon' => 'document', 'url' => route('resources.index')],
        ['title' => 'View Maintenance', 'description' => 'View maintenance requests', 'icon' => 'wrench', 'url' => '#'],
    ]" title="Quick Actions" />
</div>
