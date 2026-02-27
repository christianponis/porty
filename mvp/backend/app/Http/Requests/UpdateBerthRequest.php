<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBerthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'port_id' => ['sometimes', 'exists:ports,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'length' => ['sometimes', 'numeric', 'min:1'],
            'width' => ['sometimes', 'numeric', 'min:0.5'],
            'max_draft' => ['sometimes', 'numeric', 'min:0.5'],
            'price_per_day' => ['sometimes', 'numeric', 'min:0'],
            'price_per_month' => ['nullable', 'numeric', 'min:0'],
            'sharing_enabled' => ['boolean'],
            'nodi_value_per_day' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
