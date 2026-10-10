<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Resource extends Model
{
    use SoftDeletes;

    protected $keyType = 'uuid';
    public $incrementing = false;

    protected $fillable = [
        'owner_type',
        'owner_id',
        'name',
        'category',
        'description',
        'total_quantity',
        'available_quantity',
        'condition',
        'status',
        'available_for_borrowing',
        'max_borrowing_duration',
        'pickup_location',
        'borrowing_restrictions',
        'contact_number',
        'additional_notes',
        'photo',
        'availability_schedule',
        'lending_conditions',
        'photos',
        'building_id',
        'room_id',
    ];

    protected $casts = [
        'availability_schedule' => 'array',
        'photos' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($resource) {
            if (empty($resource->id)) {
                $resource->id = (string) \Illuminate\Support\Str::uuid();
            }

            // Calculate available quantity if not set
            if (empty($resource->available_quantity) && !empty($resource->total_quantity)) {
                $resource->available_quantity = $resource->total_quantity;
            }

            // The creator is always the owner
            $user = Auth::user();
            if ($user) {
                $resource->owner_type = 'user';
                $resource->owner_id = $user->id;
            }
        });

        static::updating(function ($resource) {
            // Prevent changing ownership after creation
            if ($resource->isDirty('owner_type') || $resource->isDirty('owner_id')) {
                $originalOwnerType = $resource->getOriginal('owner_type');
                $originalOwnerId = $resource->getOriginal('owner_id');

                if ($originalOwnerType !== $resource->owner_type || $originalOwnerId !== $resource->owner_id) {
                    // Revert ownership changes
                    $resource->owner_type = $originalOwnerType;
                    $resource->owner_id = $originalOwnerId;
                }
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function borrowRequests(): HasMany
    {
        return $this->hasMany(ResourceBorrowRequest::class);
    }

    public function isUserOwned(): bool
    {
        return $this->owner_type === 'user' && $this->owner_id !== null;
    }

    public function isSchoolOwned(): bool
    {
        return $this->owner_type === 'school';
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->isUserOwned() && $this->owner_id === $user->id;
    }
}
