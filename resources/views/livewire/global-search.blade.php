<div>
    <!-- Search Trigger Button -->
    <button wire:click="open" class="relative p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
        <x-heroicon-o-magnifying-glass class="w-6 h-6" />
        <span class="absolute top-0 right-0 w-2 h-2 bg-blue-500 rounded-full"></span>
    </button>

    <!-- Search Modal -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="close"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <!-- Search Input -->
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 mr-3" />
                            <input
                                type="text"
                                wire:model.live="query"
                                placeholder="Search users, buildings, facilities, equipment, reservations..."
                                class="flex-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                autofocus
                            />
                            <button wire:click="close" class="ml-3 p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <x-heroicon-o-x-mark class="w-5 h-5" />
                            </button>
                        </div>

                        <!-- Type Filters -->
                        <div class="flex items-center space-x-4 mt-3">
                            <label class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <input type="checkbox" wire:model.live="selectedTypes" value="users" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2">Users</span>
                            </label>
                            <label class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <input type="checkbox" wire:model.live="selectedTypes" value="buildings" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2">Buildings</span>
                            </label>
                            <label class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <input type="checkbox" wire:model.live="selectedTypes" value="facilities" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2">Facilities</span>
                            </label>
                            <label class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <input type="checkbox" wire:model.live="selectedTypes" value="equipment" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2">Equipment</span>
                            </label>
                            <label class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <input type="checkbox" wire:model.live="selectedTypes" value="reservations" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2">Reservations</span>
                            </label>
                        </div>
                    </div>

                    <!-- Results -->
                    <div class="max-h-96 overflow-y-auto">
                        @if(strlen($query) < 2)
                            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                                <x-heroicon-o-magnifying-glass class="w-8 h-8 mx-auto mb-4 text-gray-300 dark:text-gray-600" />
                                <p>Type at least 2 characters to search</p>
                            </div>
                        @elseif(empty($results))
                            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                                <x-heroicon-o-document class="w-8 h-8 mx-auto mb-4 text-gray-300 dark:text-gray-600" />
                                <p>No results found for "{{ $query }}"</p>
                            </div>
                        @else
                            @foreach($results as $type => $items)
                                @if($items->count() > 0)
                                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 capitalize">{{ $type }}</h4>
                                        <div class="space-y-2">
                                            @foreach($items as $item)
                                                <a href="{{ $item['url'] }}" class="flex items-center p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors" wire:click="close">
                                                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center mr-3">
                                                        @if($item['icon'] === 'user')
                                                            <x-heroicon-o-user class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                                        @elseif($item['icon'] === 'building')
                                                            <x-heroicon-o-building-office class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                                        @elseif($item['icon'] === 'home')
                                                            <x-heroicon-o-home class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                                        @elseif($item['icon'] === 'cube')
                                                            <x-heroicon-o-cube class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                                        @else
                                                            <x-heroicon-o-calendar class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                                        @endif
                                                    </div>
                                                    <div class="flex-1">
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item['title'] }}</p>
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $item['subtitle'] }}</p>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>

                    <!-- Footer -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('global-search.index', ['q' => $query, 'types' => implode(',', $selectedTypes)]) }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            View all results for "{{ $query }}"
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
