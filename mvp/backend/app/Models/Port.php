<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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

    public function conventions(): HasMany
    {
        return $this->hasMany(PortConvention::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Return image_url from DB if set, otherwise auto-detect from public/ports/ folder.
     */
    public function getImageUrlAttribute(?string $value): ?string
    {
        if ($value) {
            return $value;
        }

        $countryCode = match ($this->country) {
            'Italia' => 'it',
            'France' => 'fr',
            default  => null,
        };

        if (! $countryCode || ! $this->region) {
            return null;
        }

        $regionSlug = Str::slug($this->region);
        $dir = public_path("ports/{$countryCode}/{$regionSlug}");

        if (! is_dir($dir)) {
            return null;
        }

        $files = glob("{$dir}/*.{jpg,jpeg,png,webp}", GLOB_BRACE);

        if (empty($files)) {
            return null;
        }

        // Prefer a file whose name contains the city slug or port name slug
        $citySlug = Str::slug($this->city ?? '');
        $nameSlug = Str::slug($this->name ?? '');

        foreach ($files as $file) {
            $basename = pathinfo($file, PATHINFO_FILENAME);
            if ($citySlug && str_contains($basename, $citySlug)) {
                return "/ports/{$countryCode}/{$regionSlug}/" . basename($file);
            }
            if ($nameSlug && str_contains($basename, $nameSlug)) {
                return "/ports/{$countryCode}/{$regionSlug}/" . basename($file);
            }
        }

        // Fallback: first image in the folder
        return "/ports/{$countryCode}/{$regionSlug}/" . basename($files[0]);
    }
}
