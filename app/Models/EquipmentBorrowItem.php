<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentBorrowItem extends Model
{
    protected $keyType = 'uuid';
    public $incrementing = false;

    protected static function booted()
    {
        static::creating(function ($item) {
            if (empty($item->id)) {
                $item->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    protected $fillable = [
        'borrow_request_id',
        'equipment_id',
        'quantity',
        'returned_quantity',
        'condition_at_borrow',
        'condition_at_return',
        'damage_notes',
    ];

    public function borrowRequest(): BelongsTo
    {
        return $this->belongsTo(EquipmentBorrowRequest::class, 'borrow_request_id');
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }
}
