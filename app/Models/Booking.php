<?php

namespace App\Models;

use App\Enums\BookingMode;
use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $fillable = [
        'berth_id',
        'guest_id',
        'start_date',
        'end_date',
        'total_days',
        'total_price',
        'status',
        'guest_notes',
        'owner_notes',
        'cancelled_by',
        'cancelled_at',
        'booking_mode',
        'nodi_amount',
        'eur_compensation',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_price' => 'decimal:2',
            'status' => BookingStatus::class,
            'cancelled_at' => 'datetime',
            'booking_mode' => BookingMode::class,
            'nodi_amount' => 'decimal:2',
            'eur_compensation' => 'decimal:2',
        ];
    }

    // --- Relazioni ---

    public function berth(): BelongsTo
    {
        return $this->belongsTo(Berth::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    // --- Scopes ---

    public function scopePending($query)
    {
        return $query->where('status', BookingStatus::Pending);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', BookingStatus::Confirmed);
    }
}
