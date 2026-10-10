<x-app-layout>
    <x-slot name="title">Inventory Management</x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Inventory Management</h1>
        <p class="text-gray-600 dark:text-gray-400">Manage facilities, equipment, and resources inventory</p>
    </x-slot>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Facilities</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $facilities->count() }}</p>
                </div>
                <x-heroicon-o-building-office class="w-8 h-8 text-blue-600 dark:text-blue-400" />
            </div>
        </div>
        <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Equipment</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $equipment->count() }}</p>
                </div>
                <x-heroicon-o-cube class="w-8 h-8 text-purple-600 dark:text-purple-400" />
            </div>
        </div>
        <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Resources</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $resources->count() }}</p>
                </div>
                <x-heroicon-o-document class="w-8 h-8 text-green-600 dark:text-green-400" />
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="mb-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8">
                <button onclick="showTab('facilities')" id="tab-facilities" class="tab-btn active whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600 dark:text-blue-400">
                    Facilities
                </button>
                <button onclick="showTab('equipment')" id="tab-equipment" class="tab-btn whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                    Equipment
                </button>
                <button onclick="showTab('resources')" id="tab-resources" class="tab-btn whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                    Resources
                </button>
            </nav>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.getElementById('facilities-tab').style.display = 'none';
            document.getElementById('equipment-tab').style.display = 'none';
            document.getElementById('resources-tab').style.display = 'none';
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            });
            
            // Show selected tab
            document.getElementById(tabName + '-tab').style.display = 'block';
            
            // Add active class to selected button
            const activeBtn = document.getElementById('tab-' + tabName);
            activeBtn.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            activeBtn.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        }
        
        // Initialize: show facilities tab by default
        document.addEventListener('DOMContentLoaded', function() {
            showTab('facilities');
        });
    </script>

    <!-- Facilities Tab -->
    <div id="facilities-tab" style="display: none;">
        @if($facilities->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($facilities as $facility)
                    <x-card>
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $facility->name }}</h3>
                                @if($facility->room)
                                    <span class="inline-block px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full mt-1">
                                        {{ $facility->room->name }}
                                    </span>
                                @endif
                            </div>
                            <x-status-badge :status="$facility->status" />
                        </div>

                        @if($facility->description)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ \Illuminate\Support\Str::limit($facility->description, 100) }}</p>
                        @endif

                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <x-heroicon-o-users class="w-4 h-4 mr-2" />
                                Capacity: {{ $facility->capacity }} people
                            </div>
                            @if($facility->room)
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <x-heroicon-o-building-office class="w-4 h-4 mr-2" />
                                    Room: {{ $facility->room->name }}
                                </div>
                            @endif
                        </div>
                    </x-card>
                @endforeach
            </div>
        @else
            <x-card>
                <div class="text-center py-12">
                    <x-heroicon-o-building-office class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No facilities available</h3>
                </div>
            </x-card>
        @endif
    </div>

    <!-- Equipment Tab -->
    <div id="equipment-tab" style="display: none;">
        @if($equipment->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($equipment as $item)
                    <x-card>
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $item->name }}</h3>
                                @if($item->category)
                                    <span class="inline-block px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full mt-1">
                                        {{ $item->category->name }}
                                    </span>
                                @endif
                            </div>
                            <x-status-badge :status="$item->status" />
                        </div>

                        @if($item->description)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ \Illuminate\Support\Str::limit($item->description, 100) }}</p>
                        @endif

                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <x-heroicon-o-check-circle class="w-4 h-4 mr-2" />
                                Condition: {{ ucfirst($item->condition) }}
                            </div>
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <x-heroicon-o-cube class="w-4 h-4 mr-2" />
                                Stock: {{ $item->quantity_available }} / {{ $item->quantity_total }}
                            </div>
                        </div>
                    </x-card>
                @endforeach
            </div>
        @else
            <x-card>
                <div class="text-center py-12">
                    <x-heroicon-o-cube class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No equipment available</h3>
                </div>
            </x-card>
        @endif
    </div>

    <!-- Resources Tab -->
    <div id="resources-tab" style="display: none;">
        @if($resources->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($resources as $resource)
                    <x-card>
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $resource->name }}</h3>
                                @if($resource->category)
                                    <span class="inline-block px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full mt-1">
                                        {{ $resource->category }}
                                    </span>
                                @endif
                            </div>
                            <x-status-badge :status="$resource->status" />
                        </div>

                        @if($resource->description)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ \Illuminate\Support\Str::limit($resource->description, 100) }}</p>
                        @endif

                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <x-heroicon-o-user class="w-4 h-4 mr-2" />
                                Owner: {{ $resource->owner ? $resource->owner->name : 'School' }}
                            </div>
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <x-heroicon-o-check-circle class="w-4 h-4 mr-2" />
                                Condition: {{ ucfirst($resource->condition) }}
                            </div>
                        </div>
                    </x-card>
                @endforeach
            </div>
        @else
            <x-card>
                <div class="text-center py-12">
                    <x-heroicon-o-document class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No resources available</h3>
                </div>
            </x-card>
        @endif
    </div>
</x-app-layout>
