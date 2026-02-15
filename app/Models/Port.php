<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Port extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $fillable = [
        'name',
        'city',
        'province',
        'region',
        'country',
        'latitude',
        'longitude',
        'address',
        'description',
        'amenities',
        'image_url',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'amenities' => 'array',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_active' => 'boolean',
        ];
    }

    public function berths(): HasMany
    {
        return $this->hasMany(Berth::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
