<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $keyType = 'uuid';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'type',
        'name',
        'description',
        'avatar',
        'created_by',
        'is_pinned',
        'resource_type',
        'resource_id',
        'transaction_id',
        'transaction_type',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->withPivot('role', 'joined_at', 'last_read_at', 'is_muted', 'left_at');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class, 'resource_id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'resource_id');
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class, 'resource_id');
    }

    public function facilityReservation()
    {
        return $this->belongsTo(FacilityReservation::class, 'transaction_id');
    }

    public function equipmentBorrowRequest()
    {
        return $this->belongsTo(EquipmentBorrowRequest::class, 'transaction_id');
    }

    public function resourceBorrowRequest()
    {
        return $this->belongsTo(ResourceBorrowRequest::class, 'transaction_id');
    }

    public function getResourceAttribute()
    {
        return match($this->resource_type) {
            'facility' => $this->facility,
            'equipment' => $this->equipment,
            'resource' => $this->resource,
            default => null,
        };
    }

    public function getTransactionAttribute()
    {
        return match($this->transaction_type) {
            'facility_reservation' => $this->facilityReservation,
            'equipment_borrow_request' => $this->equipmentBorrowRequest,
            'resource_borrow_request' => $this->resourceBorrowRequest,
            default => null,
        };
    }
}
