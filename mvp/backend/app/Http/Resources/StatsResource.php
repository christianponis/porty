<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatsResource extends JsonResource
{
    /**
     * Create a new resource instance.
     *
     * @param  array  $resource
     */
    public function __construct($resource)
    {
        parent::__construct((object) $resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'total_ports' => $this->total_ports,
            'total_berths' => $this->total_berths,
            'total_users' => $this->total_users,
            'total_bookings' => $this->total_bookings,
        ];
    }
}
