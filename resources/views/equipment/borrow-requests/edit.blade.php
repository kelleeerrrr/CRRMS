<x-slot name="title">Edit Borrow Request</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Equipment', 'url' => '#'],
        ['title' => 'Borrow Requests', 'url' => route('equipment.borrow-requests.index')],
        ['title' => 'Edit', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Borrow Request</h1>
    <p class="text-gray-600 dark:text-gray-400">Update borrow request details</p>
</x-slot>

<x-card>
    <form action="{{ route('equipment.borrow-requests.update', $borrowRequest) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-input
                type="text"
                name="purpose"
                label="Purpose"
                placeholder="e.g., Training Session, Event"
                required
                :value="old('purpose', $borrowRequest->purpose)"
                :error="$errors->first('purpose')"
            />
            
            <x-select
                name="organization_id"
                label="Organization (Optional)"
                :options="['' => 'Select an organization (optional)'] + \App\Models\Organization::pluck('name', 'id')->toArray()"
                :value="old('organization_id', $borrowRequest->organization_id)"
            />
            
            <x-input
                type="date"
                name="start_date"
                label="Start Date"
                required
                :value="old('start_date', $borrowRequest->start_date->format('Y-m-d'))"
                :error="$errors->first('start_date')"
            />
            
            <x-input
                type="date"
                name="end_date"
                label="End Date"
                required
                :value="old('end_date', $borrowRequest->end_date->format('Y-m-d'))"
                :error="$errors->first('end_date')"
            />
        </div>
        
        <div class="mt-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Equipment Items</h3>
            <div class="space-y-4">
                @foreach($borrowRequest->items as $index => $item)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Equipment</label>
                            <select name="items[{{ $index }}][equipment_id]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                <option value="">Select equipment</option>
                                @foreach($equipment as $eq)
                                    <option value="{{ $eq->id }}" {{ $item->equipment_id === $eq->id ? 'selected' : '' }}>{{ $eq->name }} (Available: {{ $eq->available_quantity }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantity</label>
                            <input type="number" name="items[{{ $index }}][quantity]" min="1" value="{{ $item->quantity }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="flex items-end">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Returned: {{ $item->returned_quantity }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('equipment.borrow-requests.show', $borrowRequest) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Update Request
            </button>
        </div>
    </form>
</x-card>
