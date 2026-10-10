<x-slot name="title">Global Search</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Search', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Global Search</h1>
    <p class="text-gray-600 dark:text-gray-400">Search across the entire system</p>
</x-slot>

<!-- Search Form -->
<x-card class="mb-6">
    <form action="{{ route('global-search.index') }}" method="GET" class="flex items-center space-x-4">
        <div class="flex-1">
            <input
                type="text"
                name="q"
                value="{{ $query }}"
                placeholder="Search users, buildings, facilities, equipment, reservations..."
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                autofocus
            />
        </div>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            Search
        </button>
    </form>
</x-card>

<!-- Type Filters -->
<x-card class="mb-6">
    <div class="flex items-center space-x-6">
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter by type:</span>
        <label class="flex items-center">
            <input type="checkbox" name="types" value="users" {{ in_array('users', explode(',', $types)) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Users</span>
        </label>
        <label class="flex items-center">
            <input type="checkbox" name="types" value="buildings" {{ in_array('buildings', explode(',', $types)) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Buildings</span>
        </label>
        <label class="flex items-center">
            <input type="checkbox" name="types" value="facilities" {{ in_array('facilities', explode(',', $types)) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Facilities</span>
        </label>
        <label class="flex items-center">
            <input type="checkbox" name="types" value="equipment" {{ in_array('equipment', explode(',', $types)) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Equipment</span>
        </label>
        <label class="flex items-center">
            <input type="checkbox" name="types" value="reservations" {{ in_array('reservations', explode(',', $types)) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Reservations</span>
        </label>
    </div>
</x-card>

<!-- Results -->
@if(empty($query))
    <x-card>
        <div class="text-center py-12">
            <x-heroicon-o-magnifying-glass class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" />
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Start your search</h3>
            <p class="text-gray-500 dark:text-gray-400">Enter a search term to find users, buildings, facilities, equipment, and reservations.</p>
        </div>
    </x-card>
@elseif(empty($results))
    <x-card>
        <div class="text-center py-12">
            <x-heroicon-o-document class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" />
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No results found</h3>
            <p class="text-gray-500 dark:text-gray-400">No results found for "{{ $query }}". Try adjusting your search or filters.</p>
        </div>
    </x-card>
@else
    @foreach($results as $type => $items)
        @if($items->count() > 0)
            <x-card class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 capitalize">{{ $type }} ({{ $items->count() }})</h3>
                <div class="space-y-3">
                    @foreach($items as $item)
                        <a href="{{ $item['url'] }}" class="flex items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center mr-4">
                                @if($item['icon'] === 'user')
                                    <x-heroicon-o-user class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                @elseif($item['icon'] === 'building')
                                    <x-heroicon-o-building-office class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                @elseif($item['icon'] === 'home')
                                    <x-heroicon-o-home class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                @elseif($item['icon'] === 'cube')
                                    <x-heroicon-o-cube class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                @else
                                    <x-heroicon-o-calendar class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $item['title'] }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $item['subtitle'] }}</p>
                            </div>
                            <x-heroicon-o-arrow-right class="w-5 h-5 text-gray-400" />
                        </a>
                    @endforeach
                </div>
            </x-card>
        @endif
    @endforeach
@endif
