<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewVerification extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'review_id',
        'question_key',
        'answer',
    ];

    protected function casts(): array
    {
        return [
            'answer' => 'boolean',
        ];
    }

    // --- Relazioni ---

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }
}
