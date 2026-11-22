<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $resource->name }}</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Resource details</p>
            </div>
            <a href="{{ route('resources.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Back to Resources
            </a>
        </div>
    </x-slot>

    <x-card class="max-w-4xl mx-auto">
        <!-- Image at top -->
        @if($resource->photos && !empty($resource->photos))
            <div class="mb-6 rounded-lg overflow-hidden">
                <img src="{{ asset('storage/' . $resource->photos[0]) }}" alt="{{ $resource->name }}" class="w-full h-96 object-cover">
            </div>
        @elseif($resource->photo)
            <div class="mb-6 rounded-lg overflow-hidden">
                <img src="{{ asset('storage/' . $resource->photo) }}" alt="{{ $resource->name }}" class="w-full h-96 object-cover">
            </div>
        @endif

        <!-- Resource Details -->
        <div class="space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Description</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ $resource->description ?? 'No description provided' }}</p>
            </div>

            @if($resource->lending_conditions)
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Lending Conditions</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ $resource->lending_conditions }}</p>
                </div>
            @endif

            @if($resource->borrowing_restrictions)
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Borrowing Restrictions</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ $resource->borrowing_restrictions }}</p>
                </div>
            @endif

            @if($resource->additional_notes)
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Additional Notes</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ $resource->additional_notes }}</p>
                </div>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Condition</h3>
                    <p class="text-gray-900 dark:text-white">{{ ucfirst($resource->condition) }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status</h3>
                    <x-status-badge :status="$resource->status" />
                </div>
                @if($resource->category)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Category</h3>
                        <p class="text-gray-900 dark:text-white">{{ $resource->category }}</p>
                    </div>
                @endif
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Quantity</h3>
                    <p class="text-gray-900 dark:text-white">{{ $resource->total_quantity }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Available Quantity</h3>
                    <p class="text-gray-900 dark:text-white">{{ $resource->available_quantity }}</p>
                </div>
                @if($resource->max_borrowing_duration)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Max Borrow Duration</h3>
                        <p class="text-gray-900 dark:text-white">{{ $resource->max_borrowing_duration }}</p>
                    </div>
                @endif
                @if($resource->pickup_location)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Pickup Location</h3>
                        <p class="text-gray-900 dark:text-white">{{ $resource->pickup_location }}</p>
                    </div>
                @endif
                @if($resource->building)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Location</h3>
                        <p class="text-gray-900 dark:text-white">{{ $resource->building->name }}{{ $resource->room ? ' - ' . $resource->room->name : '' }}</p>
                    </div>
                @endif
                @if($resource->contact_number)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Contact Number</h3>
                        <p class="text-gray-900 dark:text-white">{{ $resource->contact_number }}</p>
                    </div>
                @endif
            </div>

            <!-- Owner Information -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Owner</h3>
                <div class="flex items-center">
                    @if($resource->owner)
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white font-semibold mr-3">
                            {{ strtoupper(substr($resource->owner->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $resource->owner->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $resource->owner->email }}</p>
                        </div>
                    @else
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-500 to-gray-600 flex items-center justify-center text-white font-semibold mr-3">
                            S
                        </div>
                        <div>
                            <p class="text-gray-900 dark:text-white font-medium">School</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">School-owned resource</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            @if(auth()->id() !== $resource->owner_id && $resource->status === 'available' && auth()->user()->roles()->first()?->name === 'user')
                <div class="flex space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('resource-borrow-requests.create', ['resource_id' => $resource->id]) }}" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-center font-medium">
                        Borrow
                    </a>
                    <form action="{{ route('chat.with-owner') }}" method="POST" class="flex-1">
                        @csrf
                        <input type="hidden" name="resource_type" value="resource">
                        <input type="hidden" name="resource_id" value="{{ $resource->id }}">
                        <button type="submit" class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                            Chat
                        </button>
                    </form>
                </div>
            @endif

            @if(auth()->id() === $resource->owner_id)
                <div class="flex space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('resources.edit', $resource) }}" class="flex-1 px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-center font-medium">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('resources.destroy', $resource) }}" onsubmit="return confirm('Are you sure?')" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                            Delete
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </x-card>
</x-app-layout>
