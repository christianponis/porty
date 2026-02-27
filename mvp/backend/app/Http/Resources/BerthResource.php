<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BerthResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->title,
            'port' => new PortResource($this->whenLoaded('port')),
            'description' => $this->description,
            'length' => (float) $this->length_m,
            'width' => (float) $this->width_m,
            'max_draft' => (float) $this->max_draft_m,
            'price_per_day' => (float) $this->price_per_day,
            'price_per_month' => $this->price_per_month ? (float) $this->price_per_month : null,
            'is_active' => $this->is_active,
            'status' => $this->status?->value,
            'rating_level' => $this->rating_level?->value,
            'grey_anchor_count' => $this->grey_anchor_count,
            'blue_anchor_count' => $this->blue_anchor_count,
            'gold_anchor_count' => $this->gold_anchor_count,
            'review_count' => $this->review_count ?? 0,
            'review_average' => $this->review_average ? (float) $this->review_average : null,
            'sharing_enabled' => $this->sharing_enabled,
            'nodi_value_per_day' => $this->nodi_value_per_day ? (float) $this->nodi_value_per_day : null,
            'images' => $this->formatImages(),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }

    protected function formatImages(): array
    {
        if (! $this->images || ! is_array($this->images)) {
            return [];
        }

        return array_map(function ($image) {
            if (str_starts_with($image, 'http')) {
                return $image;
            }
            return Storage::url($image);
        }, $this->images);
    }
}
