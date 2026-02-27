<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\StatsResource;
use App\Models\Berth;
use App\Models\Booking;
use App\Models\Port;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    /**
     * Return platform statistics.
     */
    public function __invoke(): JsonResponse
    {
        $stats = [
            'total_ports' => Port::active()->count(),
            'total_berths' => Berth::active()->count(),
            'total_users' => User::where('is_active', true)->count(),
            'total_bookings' => Booking::where('status', BookingStatus::Completed)->count(),
        ];

        return response()->json([
            'data' => new StatsResource($stats),
        ]);
    }
}
