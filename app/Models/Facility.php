<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends Model
{
    use SoftDeletes;

    protected $keyType = 'uuid';
    public $incrementing = false;

    protected $fillable = [
        'room_id',
        'name',
        'type',
        'category',
        'capacity',
        'location_address',
        'amenities',
        'photos',
        'rules',
        'availability_schedule',
        'status',
        'operating_days',
        'opening_time',
        'closing_time',
        'max_reservation_duration',
        'advance_booking_requirement',
        'person_in_charge',
        'contact_information',
        'special_instructions',
    ];

    protected $casts = [
        'amenities' => 'array',
        'photos' => 'array',
        'availability_schedule' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($facility) {
            if (empty($facility->id)) {
                $facility->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(FacilityReservation::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(FacilitySchedule::class);
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class);
    }
}
