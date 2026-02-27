<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'berth_id' => $this->berth_id,
            'guest' => $this->whenLoaded('guest', function () {
                return [
                    'id' => $this->guest->id,
                    'name' => $this->guest->name,
                ];
            }),
            'rating_ormeggio' => $this->rating_ormeggio,
            'rating_servizi' => $this->rating_servizi,
            'rating_posizione' => $this->rating_posizione,
            'rating_qualita_prezzo' => $this->rating_qualita_prezzo,
            'rating_accoglienza' => $this->rating_accoglienza,
            'average_rating' => (float) $this->average_rating,
            'comment' => $this->comment,
            'is_verified' => $this->is_verified,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
