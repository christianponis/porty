<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSelfAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'answers' => ['required', 'array'],
            'answers.*.question_id' => ['required', 'exists:assessment_questions,id'],
            'answers.*.answer_value' => ['required', 'integer', 'min:0', 'max:10'],
            'answers.*.photo' => ['nullable', 'image', 'max:5120'],
        ];
    }
}
