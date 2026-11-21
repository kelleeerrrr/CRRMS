<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BorrowerIssueReport extends Model
{
    use HasUuids;

    public const KINDS = [
        'damage' => 'Damage',
        'missing_item' => 'Missing item',
        'facility_problem' => 'Facility problem',
    ];

    protected $fillable = ['user_id', 'kind', 'location', 'description', 'photos'];

    protected $casts = ['photos' => 'array'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
