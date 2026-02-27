<?php

namespace App\Models;

use App\Enums\ConventionCategory;
use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortConvention extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'port_id',
        'name',
        'description',
        'category',
        'address',
        'phone',
        'email',
        'website',
        'discount_type',
        'discount_value',
        'discount_description',
        'logo',
        'image',
        'latitude',
        'longitude',
        'is_active',
        'valid_from',
        'valid_until',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'category'       => ConventionCategory::class,
            'discount_type'  => DiscountType::class,
            'discount_value' => 'decimal:2',
            'latitude'       => 'decimal:7',
            'longitude'      => 'decimal:7',
            'is_active'      => 'boolean',
            'valid_from'     => 'date',
            'valid_until'    => 'date',
            'sort_order'     => 'integer',
        ];
    }

    public function port(): BelongsTo
    {
        return $this->belongsTo(Port::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', now());
            });
    }
}
