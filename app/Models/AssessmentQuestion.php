<?php

namespace App\Models;

use App\Enums\AssessmentCategory;
use Illuminate\Database\Eloquent\Model;

class AssessmentQuestion extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'category',
        'question_text',
        'question_type',
        'requires_photo',
        'weight',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'category' => AssessmentCategory::class,
            'requires_photo' => 'boolean',
            'weight' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // --- Scopes ---

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, AssessmentCategory $category)
    {
        return $query->where('category', $category)->orderBy('sort_order');
    }
}
