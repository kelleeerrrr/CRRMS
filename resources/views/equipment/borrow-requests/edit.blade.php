<x-app-layout :breadcrumbs="[
        ['title' => 'Equipment', 'url' => '#'],
        ['title' => 'Borrow Requests', 'url' => route('equipment.borrow-requests.index')],
        ['title' => 'Edit', 'url' => null],
    ]">
<x-slot name="title">Edit Borrow Request</x-slot>

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
                name="borrow_date"
                label="Borrow Date"
                required
                :value="old('borrow_date', $borrowRequest->borrow_date->format('Y-m-d'))"
                :error="$errors->first('borrow_date')"
            />
            
            <x-input
                type="date"
                name="return_date"
                label="Return Date"
                required
                :value="old('return_date', $borrowRequest->return_date->format('Y-m-d'))"
                :error="$errors->first('return_date')"
            />
        </div>
        
        <div class="mt-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Equipment Items</h3>
            <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                @foreach($borrowRequest->items as $item)
                    <li>{{ $item->equipment?->name ?? 'Equipment' }} · Quantity {{ $item->quantity }}</li>
                @endforeach
            </ul>
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
</x-app-layout>
