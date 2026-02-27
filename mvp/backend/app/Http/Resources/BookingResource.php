<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'berth' => new BerthResource($this->whenLoaded('berth')),
            'guest_id' => $this->guest_id,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'total_days' => $this->total_days,
            'total_price' => (float) $this->total_price,
            'status' => $this->status?->value,
            'booking_mode' => $this->booking_mode?->value,
            'nodi_amount' => $this->nodi_amount ? (float) $this->nodi_amount : null,
            'eur_compensation' => $this->eur_compensation ? (float) $this->eur_compensation : null,
            'boat_name' => $this->boat_name,
            'boat_length' => $this->boat_length,
            'boat_width' => $this->boat_width,
            'boat_draft' => $this->boat_draft,
            'commission_amount' => $this->when(
                $this->relationLoaded('transactions'),
                fn () => $this->transactions->sum('commission_amount')
            ),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
