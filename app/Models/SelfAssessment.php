<?php

namespace App\Models;

use App\Enums\AssessmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SelfAssessment extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'berth_id',
        'owner_id',
        'status',
        'total_score',
        'anchor_count',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => AssessmentStatus::class,
            'total_score' => 'decimal:2',
            'submitted_at' => 'datetime',
        ];
    }

    // --- Relazioni ---

    public function berth(): BelongsTo
    {
        return $this->belongsTo(Berth::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SelfAssessmentAnswer::class);
    }
}
