<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rating_ormeggio' => ['required', 'integer', 'between:1,5'],
            'rating_servizi' => ['required', 'integer', 'between:1,5'],
            'rating_posizione' => ['required', 'integer', 'between:1,5'],
            'rating_qualita_prezzo' => ['required', 'integer', 'between:1,5'],
            'rating_accoglienza' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
