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
            'berth_id' => ['required', 'exists:berths,id'],
            'start_date' => ['required', 'date', 'after:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'booking_mode' => ['required', 'in:rental,sharing,sharing_compensation'],
            'boat_name' => ['required', 'string'],
            'boat_length' => ['required', 'numeric'],
            'boat_width' => ['required', 'numeric'],
            'boat_draft' => ['required', 'numeric'],
        ];
    }
}
