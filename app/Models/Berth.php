<?php

namespace App\Models;

use App\Enums\BerthStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Berth extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $fillable = [
        'owner_id',
        'port_id',
        'code',
        'title',
        'description',
        'length_m',
        'width_m',
        'max_draft_m',
        'price_per_day',
        'price_per_week',
        'price_per_month',
        'amenities',
        'images',
        'status',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'amenities' => 'array',
            'images' => 'array',
            'status' => BerthStatus::class,
            'price_per_day' => 'decimal:2',
            'price_per_week' => 'decimal:2',
            'price_per_month' => 'decimal:2',
            'length_m' => 'decimal:2',
            'width_m' => 'decimal:2',
            'max_draft_m' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // --- Relazioni ---

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function port(): BelongsTo
    {
        return $this->belongsTo(Port::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(BerthAvailability::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    // --- Scopes ---

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', BerthStatus::Available);
    }
}
