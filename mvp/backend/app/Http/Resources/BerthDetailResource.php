<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class BerthDetailResource extends BerthResource
{
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        $data['reviews'] = ReviewResource::collection($this->whenLoaded('reviews'));
        $data['availability'] = $this->whenLoaded('availabilities', function () {
            return $this->availabilities->map(function ($availability) {
                return [
                    'id' => $availability->id,
                    'start_date' => $availability->start_date->toDateString(),
                    'end_date' => $availability->end_date->toDateString(),
                    'is_available' => $availability->is_available,
                    'note' => $availability->note,
                ];
            });
        });
        $data['owner'] = $this->whenLoaded('owner', function () {
            return [
                'id' => $this->owner->id,
                'name' => $this->owner->name,
            ];
        });

        return $data;
    }
}
