<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use SoftDeletes;

    protected $keyType = 'uuid';
    public $incrementing = false;

    protected static function booted()
    {
        static::creating(function ($equipment) {
            if (empty($equipment->id)) {
                $equipment->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    protected $fillable = [
        'name',
        'equipment_code',
        'category_id',
        'equipment_category',
        'asset_number',
        'barcode',
        'qr_code',
        'brand',
        'model',
        'serial_number',
        'purchase_date',
        'warranty_expiry',
        'supplier',
        'cost',
        'building_id',
        'location_building',
        'room_id',
        'room_storage',
        'condition',
        'total_quantity',
        'availability_status',
        'available_for_borrowing',
        'max_borrowing_duration',
        'advance_booking_requirement',
        'borrowing_restrictions',
        'responsible_office',
        'additional_notes',
        'equipment_photo',
        'status',
        'description',
        'specifications',
        'photos',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'cost' => 'decimal:2',
        'specifications' => 'array',
        'photos' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(EquipmentCategory::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function borrowItems(): HasMany
    {
        return $this->hasMany(EquipmentBorrowItem::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(EquipmentHistory::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(InventoryTransfer::class);
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function getAvailableQuantityAttribute(): int
    {
        $total = $this->total_quantity ?? 0;
        
        // Subtract units currently borrowed (approved and checked out, minus returned)
        $borrowed = EquipmentBorrowItem::whereHas('borrowRequest', function ($query) {
            $query->whereIn('status', ['approved', 'in_use']);
        })->where('equipment_id', $this->id)
          ->selectRaw('SUM(quantity - COALESCE(returned_quantity, 0)) as net_borrowed')
          ->value('net_borrowed') ?? 0;
        
        // Subtract units reserved for approved future borrowing (not yet checked out)
        $reserved = EquipmentBorrowItem::whereHas('borrowRequest', function ($query) {
            $query->where('status', 'approved')
                  ->where('borrow_date', '>', now());
        })->where('equipment_id', $this->id)
          ->sum('quantity');
        
        return max(0, $total - $borrowed - $reserved);
    }

    public function isAvailableForBorrowing(): bool
    {
        return $this->available_for_borrowing 
            && in_array($this->availability_status, ['Available'])
            && $this->getAvailableQuantityAttribute() > 0;
    }
}
