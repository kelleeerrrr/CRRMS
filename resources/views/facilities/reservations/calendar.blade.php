<x-slot name="title">Reservation Calendar</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Facilities', 'url' => '#'],
        ['title' => 'Reservations', 'url' => route('facilities.reservations.index')],
        ['title' => 'Calendar', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reservation Calendar</h1>
            <p class="text-gray-600 dark:text-gray-400">View and manage facility reservations</p>
        </div>
        @if(auth()->user()->can('create', \App\Models\FacilityReservation::class))
            <a href="{{ route('facilities.reservations.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                New Reservation
            </a>
        @endif
    </div>
</x-slot>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Calendar -->
    <div class="lg:col-span-3">
        <x-calendar :events="$reservations->map(fn($r) => [
            'title' => $r->title,
            'date' => $r->start_date,
            'color' => match($r->status) {
                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                'cancelled' => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
                default => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            }
        ])->toArray()" />
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Legend -->
        <x-card title="Status Legend">
            <div class="space-y-3">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                    <span class="text-sm text-gray-700 dark:text-gray-200">Pending</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                    <span class="text-sm text-gray-700 dark:text-gray-200">Approved</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                    <span class="text-sm text-gray-700 dark:text-gray-200">Rejected</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-gray-500"></span>
                    <span class="text-sm text-gray-700 dark:text-gray-200">Cancelled</span>
                </div>
            </div>
        </x-card>

        <!-- Upcoming Reservations -->
        <x-card title="Upcoming Reservations">
            <div class="space-y-3">
                @foreach($reservations->where('start_date', '>=', now())->take(5) as $reservation)
                    <a href="{{ route('facilities.reservations.show', $reservation) }}" class="block p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 rounded-full {{ match($reservation->status) {
                                'pending' => 'bg-yellow-500',
                                'approved' => 'bg-green-500',
                                'rejected' => 'bg-red-500',
                                'cancelled' => 'bg-gray-500',
                                default => 'bg-blue-500',
                            } }}"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $reservation->title }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $reservation->start_date->format('M d') }} - {{ $reservation->facility->name }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
                @if($reservations->where('start_date', '>=', now())->count() === 0)
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No upcoming reservations</p>
                @endif
            </div>
        </x-card>

        <!-- Quick Actions -->
        <x-card title="Quick Actions">
            <div class="space-y-2">
                <a href="{{ route('facilities.reservations.index') }}" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <x-heroicon-o-list class="w-5 h-5 mr-3" />
                    List View
                </a>
                @if(auth()->user()->can('manage-facilities'))
                    <a href="#" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <x-heroicon-o-chart-bar class="w-5 h-5 mr-3" />
                        View Reports
                    </a>
                @endif
            </div>
        </x-card>
    </div>
</div>
