<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AssessmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'berth_id' => $this->berth_id,
            'status' => $this->status?->value,
            'total_score' => $this->total_score ? (float) $this->total_score : null,
            'anchor_count' => $this->anchor_count,
            'answers' => $this->whenLoaded('answers', function () {
                return $this->answers->map(function ($answer) {
                    return [
                        'id' => $answer->id,
                        'question_id' => $answer->question_id,
                        'answer_value' => $answer->answer_value,
                        'photo_path' => $answer->photo_path ? Storage::url($answer->photo_path) : null,
                        'question' => $answer->relationLoaded('question') ? [
                            'id' => $answer->question->id,
                            'category' => $answer->question->category?->value,
                            'question_text' => $answer->question->question_text,
                            'question_type' => $answer->question->question_type,
                            'requires_photo' => $answer->question->requires_photo,
                            'weight' => (float) $answer->question->weight,
                        ] : null,
                    ];
                });
            }),
            'submitted_at' => $this->submitted_at?->toISOString(),
        ];
    }
}
