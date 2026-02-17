<?php

namespace App\Models;

use App\Enums\CertificationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certification extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'berth_id',
        'inspector_id',
        'status',
        'total_score',
        'anchor_count',
        'video_url',
        'notes',
        'inspection_date',
        'valid_until',
        'paid_at',
        'paid_amount',
    ];

    protected function casts(): array
    {
        return [
            'status' => CertificationStatus::class,
            'total_score' => 'decimal:2',
            'inspection_date' => 'date',
            'valid_until' => 'date',
            'paid_at' => 'datetime',
            'paid_amount' => 'decimal:2',
        ];
    }

    // --- Relazioni ---

    public function berth(): BelongsTo
    {
        return $this->belongsTo(Berth::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    // --- Helpers ---

    public function isValid(): bool
    {
        return $this->status === CertificationStatus::Completed
            && $this->valid_until
            && $this->valid_until->isFuture();
    }
}
