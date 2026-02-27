<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConventionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'port_id'              => $this->port_id,
            'port'                 => new PortResource($this->whenLoaded('port')),
            'name'                 => $this->name,
            'description'          => $this->description,
            'category'             => $this->category?->value,
            'category_label'       => $this->category?->label(),
            'address'              => $this->address,
            'phone'                => $this->phone,
            'email'                => $this->email,
            'website'              => $this->website,
            'discount_type'        => $this->discount_type?->value,
            'discount_value'       => $this->discount_value,
            'discount_description' => $this->discount_description,
            'logo'                 => $this->logo,
            'image'                => $this->image,
            'latitude'             => $this->latitude,
            'longitude'            => $this->longitude,
            'is_active'            => $this->is_active,
            'valid_from'           => $this->valid_from?->toDateString(),
            'valid_until'          => $this->valid_until?->toDateString(),
            'sort_order'           => $this->sort_order,
            'created_at'           => $this->created_at?->toISOString(),
            'updated_at'           => $this->updated_at?->toISOString(),
        ];
    }
}
