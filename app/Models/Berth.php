<?php

namespace App\Models;

use App\Enums\BerthStatus;
use App\Enums\RatingLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'sharing_enabled',
        'booking_modes',
        'nodi_value_per_day',
        'rating_level',
        'grey_anchor_count',
        'blue_anchor_count',
        'gold_anchor_count',
        'review_count',
        'review_average',
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
            'sharing_enabled' => 'boolean',
            'booking_modes' => 'array',
            'nodi_value_per_day' => 'decimal:2',
            'rating_level' => RatingLevel::class,
            'review_average' => 'decimal:2',
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

    public function selfAssessment(): HasOne
    {
        return $this->hasOne(SelfAssessment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class);
    }

    public function latestCertification(): HasOne
    {
        return $this->hasOne(Certification::class)->latestOfMany();
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

    public function scopeSharingEnabled($query)
    {
        return $query->where('sharing_enabled', true);
    }

    // --- Helpers ---

    public function getEffectiveAnchorCount(): int
    {
        return $this->gold_anchor_count
            ?? $this->blue_anchor_count
            ?? $this->grey_anchor_count
            ?? 0;
    }

    public function getEffectiveRatingLevel(): ?RatingLevel
    {
        if ($this->gold_anchor_count && $this->latestCertification?->isValid()) {
            return RatingLevel::Gold;
        }

        if ($this->blue_anchor_count) {
            return RatingLevel::Blue;
        }

        if ($this->grey_anchor_count) {
            return RatingLevel::Grey;
        }

        return null;
    }

    public function getNodiMultiplier(): int
    {
        $anchors = $this->getEffectiveAnchorCount();
        $multipliers = config('porty.nodi.multipliers', []);

        return $multipliers[$anchors] ?? 1;
    }
}
