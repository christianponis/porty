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
            'port_id' => ['required', 'exists:ports,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'length' => ['required', 'numeric', 'min:1'],
            'width' => ['required', 'numeric', 'min:0.5'],
            'max_draft' => ['required', 'numeric', 'min:0.5'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'price_per_month' => ['nullable', 'numeric', 'min:0'],
            'sharing_enabled' => ['boolean'],
            'nodi_value_per_day' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
