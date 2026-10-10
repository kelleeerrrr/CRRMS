<x-app-layout>
    <x-slot name="title">Create Borrow Request</x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Borrow Equipment</h1>
        <p class="text-gray-600 dark:text-gray-400">Request equipment for borrowing</p>
    </x-slot>

@php
    $selectedEquipment = $equipmentId ? $equipment->firstWhere('id', $equipmentId) : null;
@endphp

@if($selectedEquipment)
    <!-- Equipment Details Section -->
    <x-card class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Equipment Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @if($selectedEquipment->equipment_photo)
                <div class="md:col-span-1">
                    <img src="{{ asset('storage/' . $selectedEquipment->equipment_photo) }}" alt="{{ $selectedEquipment->name }}" class="w-full h-48 object-cover rounded-lg">
                </div>
            @endif
            <div class="md:col-span-3 space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Equipment Name</label>
                    <p class="text-gray-900 dark:text-white font-semibold">{{ $selectedEquipment->name }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Equipment Code</label>
                        <p class="text-gray-900 dark:text-white">{{ $selectedEquipment->equipment_code }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</label>
                        <p class="text-gray-900 dark:text-white">{{ $selectedEquipment->equipment_category }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Building</label>
                        <p class="text-gray-900 dark:text-white">{{ $selectedEquipment->location_building }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Room/Storage</label>
                        <p class="text-gray-900 dark:text-white">{{ $selectedEquipment->room_storage ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Quantity</label>
                        <p class="text-gray-900 dark:text-white">{{ $selectedEquipment->total_quantity }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Available Quantity</label>
                        <p class="text-green-600 dark:text-green-400 font-semibold">{{ $selectedEquipment->available_quantity }}</p>
                    </div>
                </div>
                @if($selectedEquipment->max_borrowing_duration)
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Max Borrowing Duration</label>
                        <p class="text-gray-900 dark:text-white">{{ $selectedEquipment->max_borrowing_duration }} days</p>
                    </div>
                @endif
                @if($selectedEquipment->borrowing_restrictions)
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Borrowing Restrictions</label>
                        <p class="text-gray-900 dark:text-white text-sm">{{ $selectedEquipment->borrowing_restrictions }}</p>
                    </div>
                @endif
            </div>
        </div>
    </x-card>
@endif

<x-card>
    <form action="{{ route('equipment.borrow-requests.store') }}" method="POST">
        @csrf
        <input type="hidden" name="equipment_id" value="{{ $equipmentId }}">
        
        <!-- Borrower Information -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Borrower Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Borrower's Name</label>
                    <input type="text" value="{{ auth()->user()->name }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white" readonly>
                </div>
                <div>
                    <x-input
                        type="text"
                        name="contact_number"
                        label="Contact Number"
                        placeholder="Enter your contact number"
                        :value="auth()->user()->phone_number ?? old('contact_number')"
                        :error="$errors->first('contact_number')"
                    />
                </div>
            </div>
        </div>
        
        <!-- Borrowing Details -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Borrowing Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($selectedEquipment)
                    <div>
                        <x-input
                            type="number"
                            name="quantity"
                            label="Quantity to Borrow *"
                            placeholder="1"
                            min="1"
                            :max="$selectedEquipment->available_quantity"
                            required
                            :value="old('quantity', 1)"
                            :error="$errors->first('quantity')"
                        />
                        <p class="text-xs text-gray-500 mt-1">Available: {{ $selectedEquipment->available_quantity }} of {{ $selectedEquipment->total_quantity }}</p>
                    </div>
                @else
                    <div>
                        <x-input
                            type="number"
                            name="quantity"
                            label="Quantity to Borrow *"
                            placeholder="1"
                            min="1"
                            required
                            :value="old('quantity', 1)"
                            :error="$errors->first('quantity')"
                        />
                    </div>
                @endif
                
                <div>
                    <x-input
                        type="datetime-local"
                        name="borrow_date"
                        label="Borrow Date and Time *"
                        required
                        :value="old('borrow_date')"
                        :error="$errors->first('borrow_date')"
                    />
                </div>
                
                <div>
                    <x-input
                        type="datetime-local"
                        name="return_date"
                        label="Expected Return Date and Time *"
                        required
                        :value="old('return_date')"
                        :error="$errors->first('return_date')"
                    />
                </div>
                
                <div class="md:col-span-2">
                    <x-input
                        type="text"
                        name="purpose"
                        label="Purpose of Borrowing *"
                        placeholder="e.g., Training Session, Event"
                        required
                        :value="old('purpose')"
                        :error="$errors->first('purpose')"
                    />
                </div>
                
                <div class="md:col-span-2">
                    <x-input
                        type="text"
                        name="intended_use"
                        label="Intended Use / Location (Optional)"
                        placeholder="e.g., Room 101, Main Hall"
                        :value="old('intended_use')"
                        :error="$errors->first('intended_use')"
                    />
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Additional Notes (Optional)</label>
                    <textarea
                        name="notes"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                        placeholder="Any additional information..."
                    >{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Agreement -->
        <div class="mb-6">
            <label class="flex items-start space-x-3">
                <input type="checkbox" name="agreement" value="1" required class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <span class="text-sm text-gray-700 dark:text-gray-300">
                    I agree to take proper care of the borrowed equipment and return it by the approved return date. I understand that I am responsible for the equipment while it is in my custody.
                </span>
            </label>
            @error('agreement')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="flex justify-end space-x-3">
            <a href="{{ route('equipment.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Submit Borrowing Request
            </button>
        </div>
    </form>
</x-card>
</x-app-layout>
