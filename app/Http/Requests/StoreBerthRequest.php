<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBerthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'port_id' => 'required|exists:ports,id',
            'code' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'length_m' => 'required|numeric|min:1|max:100',
            'width_m' => 'required|numeric|min:1|max:30',
            'max_draft_m' => 'nullable|numeric|min:0.5|max:15',
            'price_per_day' => 'required|numeric|min:1',
            'price_per_week' => 'nullable|numeric|min:1',
            'price_per_month' => 'nullable|numeric|min:1',
            'amenities' => 'nullable|array',
            'availability_start' => 'nullable|date|after:today',
            'availability_end' => 'nullable|date|after:availability_start',
        ];
    }

    public function messages(): array
    {
        return [
            'port_id.required' => 'Seleziona un porto.',
            'code.required' => 'Il codice del posto e obbligatorio.',
            'title.required' => 'Il titolo e obbligatorio.',
            'length_m.required' => 'La lunghezza e obbligatoria.',
            'width_m.required' => 'La larghezza e obbligatoria.',
            'price_per_day.required' => 'Il prezzo giornaliero e obbligatorio.',
            'price_per_day.min' => 'Il prezzo deve essere almeno 1 EUR.',
            'availability_end.after' => 'La data di fine deve essere successiva alla data di inizio.',
        ];
    }
}
