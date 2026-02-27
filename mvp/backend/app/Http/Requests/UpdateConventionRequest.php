<?php

namespace App\Http\Requests;

use App\Enums\ConventionCategory;
use App\Enums\DiscountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConventionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'port_id'              => ['sometimes', 'exists:ports,id'],
            'name'                 => ['sometimes', 'string', 'max:255'],
            'description'          => ['nullable', 'string'],
            'category'             => ['sometimes', Rule::enum(ConventionCategory::class)],
            'address'              => ['nullable', 'string', 'max:500'],
            'phone'                => ['nullable', 'string', 'max:50'],
            'email'                => ['nullable', 'email', 'max:255'],
            'website'              => ['nullable', 'url', 'max:500'],
            'discount_type'        => ['sometimes', Rule::enum(DiscountType::class)],
            'discount_value'       => ['nullable', 'numeric', 'min:0'],
            'discount_description' => ['nullable', 'string', 'max:500'],
            'latitude'             => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'            => ['nullable', 'numeric', 'between:-180,180'],
            'is_active'            => ['boolean'],
            'valid_from'           => ['nullable', 'date'],
            'valid_until'          => ['nullable', 'date', 'after_or_equal:valid_from'],
            'sort_order'           => ['nullable', 'integer', 'min:0'],
        ];
    }
}
