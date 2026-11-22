@props(['equipment' => null, 'borrowRequest' => null])

@if($equipment)
<div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-4 cursor-pointer hover:shadow-md transition-shadow">
    <div class="flex items-start space-x-4">
        @if($equipment->photos && !empty($equipment->photos))
            <img src="{{ asset('storage/' . $equipment->photos[0]) }}" alt="{{ $equipment->name }}" class="w-16 h-16 rounded-lg object-cover">
        @elseif($equipment->equipment_photo)
            <img src="{{ asset('storage/' . $equipment->equipment_photo) }}" alt="{{ $equipment->name }}" class="w-16 h-16 rounded-lg object-cover">
        @else
            <div class="w-16 h-16 rounded-lg bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center text-white font-bold text-xl">
                {{ strtoupper(substr($equipment->name, 0, 2)) }}
            </div>
        @endif
        
        <div class="flex-1">
            <h3 class="font-semibold text-gray-900 dark:text-white text-lg">{{ $equipment->name }}</h3>
            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1 space-y-1">
                @if($equipment->category)
                    <p><span class="font-medium">Category:</span> {{ $equipment->category->name ?? $equipment->equipment_category ?? 'N/A' }}</p>
                @endif
                @if($equipment->code)
                    <p><span class="font-medium">Code:</span> {{ $equipment->code }}</p>
                @endif
                @if($equipment->asset_tag)
                    <p><span class="font-medium">Asset Tag:</span> {{ $equipment->asset_tag }}</p>
                @endif
                @if($equipment->building)
                    <p><span class="font-medium">Location:</span> {{ $equipment->building->name }}</p>
                @endif
                <p><span class="font-medium">Quantity:</span> {{ $equipment->total_quantity }} total, {{ $equipment->available_quantity }} available</p>
            </div>
            
            @if($borrowRequest)
                <div class="mt-3 pt-3 border-t border-red-200 dark:border-red-700">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Borrow Request Status</p>
                    <div class="flex items-center space-x-2 mt-1">
                        <x-status-badge :status="$borrowRequest->status" />
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            Requested: {{ $borrowRequest->quantity }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $borrowRequest->borrow_date ? $borrowRequest->borrow_date->format('M d, Y') : 'N/A' }} - 
                        {{ $borrowRequest->return_date ? $borrowRequest->return_date->format('M d, Y') : 'N/A' }}
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <div class="flex items-center justify-end space-x-2 mt-3">
        @if($borrowRequest)
            <a href="{{ route('equipment.borrow-requests.show', $borrowRequest) }}" class="text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium">
                View Request
            </a>
        @endif
    </div>
</div>
@endif
