<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipmentBorrowRequest extends Model
{
    use SoftDeletes;

    protected $keyType = 'uuid';
    public $incrementing = false;

    protected static function booted()
    {
        static::creating(function ($request) {
            if (empty($request->id)) {
                $request->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    protected $fillable = [
        'user_id',
        'organization_id',
        'status',
        'borrow_date',
        'return_date',
        'purpose',
        'notes',
        'approval_notes',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'checked_out_by',
        'checked_out_at',
        'returned_by',
        'returned_at',
        'cancelled_by',
        'cancelled_at',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'return_date' => 'date',
        'approved_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'returned_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function checkedOutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    public function returnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(EquipmentBorrowItem::class, 'borrow_request_id');
    }
}
