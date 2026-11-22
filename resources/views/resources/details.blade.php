<x-inventory-detail-content
    :description="$resource->description"
    :photos="array_values(array_unique(array_filter([$resource->photo, ...($resource->photos ?? [])])))"
    :sections="[
        'Resource information' => [
            'Category' => $resource->category,
            'Condition' => $resource->condition ? ucfirst($resource->condition) : null,
            'Building' => $resource->building?->name,
            'Room' => $resource->room?->name,
        ],
        'Availability and borrowing' => [
            'Available for borrowing' => $resource->available_for_borrowing === null ? null : ($resource->available_for_borrowing ? 'Yes' : 'No'),
            'Available quantity' => $resource->available_quantity,
            'Total quantity' => $resource->total_quantity,
            'Maximum duration (days)' => $resource->max_borrowing_duration,
            'Pickup or handover location' => $resource->pickup_location,
            'Restrictions and instructions' => $resource->borrowing_restrictions,
            'Lending conditions' => $resource->lending_conditions,
        ],
        'Owner and contact' => [
            'Owner' => $resource->owner?->name ?? 'School',
            'Email' => $resource->owner?->email,
            'Contact number' => $resource->contact_number,
            'Additional notes' => $resource->additional_notes,
        ],
    ]"
/>
