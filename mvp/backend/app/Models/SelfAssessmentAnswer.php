<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SelfAssessmentAnswer extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'self_assessment_id',
        'question_id',
        'answer_value',
        'photo_path',
    ];

    // --- Relazioni ---

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(SelfAssessment::class, 'self_assessment_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(AssessmentQuestion::class, 'question_id');
    }
}
