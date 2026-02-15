<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'berth_id' => 'required|exists:berths,id',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'guest_notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.required' => 'La data di arrivo e obbligatoria.',
            'start_date.after' => 'La data di arrivo deve essere futura.',
            'end_date.required' => 'La data di partenza e obbligatoria.',
            'end_date.after' => 'La data di partenza deve essere successiva alla data di arrivo.',
        ];
    }
}
