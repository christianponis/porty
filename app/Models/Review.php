<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'booking_id',
        'berth_id',
        'guest_id',
        'rating_ormeggio',
        'rating_servizi',
        'rating_posizione',
        'rating_qualita_prezzo',
        'rating_accoglienza',
        'average_rating',
        'comment',
        'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'average_rating' => 'decimal:2',
            'is_verified' => 'boolean',
        ];
    }

    // --- Relazioni ---

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function berth(): BelongsTo
    {
        return $this->belongsTo(Berth::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_id');
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(ReviewVerification::class);
    }
}
