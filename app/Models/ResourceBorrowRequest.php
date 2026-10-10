<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceBorrowRequest extends Model
{
    use SoftDeletes;

    protected $keyType = 'uuid';
    public $incrementing = false;

    protected $fillable = [
        'resource_id',
        'user_id',
        'approved_by',
        'borrower_name',
        'contact_number',
        'email',
        'quantity',
        'start_date',
        'end_date',
        'actual_return_date',
        'status',
        'purpose',
        'intended_use',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'actual_return_date' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($request) {
            if (empty($request->id)) {
                $request->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function canBeApprovedBy($user): bool
    {
        $resource = $this->resource;

        // School Officer can approve school-owned resources
        if ($resource->isSchoolOwned() && $user->can('approve_school_resource_requests')) {
            return true;
        }

        // Resource owner can approve their own resources
        if ($resource->isUserOwned() && $resource->owner_id === $user->id) {
            return true;
        }

        return false;
    }
}
