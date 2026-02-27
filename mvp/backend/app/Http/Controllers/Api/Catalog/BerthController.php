<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Resources\BerthDetailResource;
use App\Http\Resources\BerthResource;
use App\Models\Berth;
use Illuminate\Http\JsonResponse;

class BerthController extends Controller
{
    /**
     * Show a single berth with port, reviews, and availability.
     */
    public function show(Berth $berth): JsonResponse
    {
        $berth->load(['port', 'reviews.guest', 'availabilities', 'owner']);

        return response()->json([
            'data' => new BerthDetailResource($berth),
        ]);
    }

    /**
     * Top 6 berths by review average (where review_count > 0, is_active).
     */
    public function top(): JsonResponse
    {
        $berths = Berth::query()
            ->active()
            ->where('review_count', '>', 0)
            ->orderByDesc('review_average')
            ->limit(6)
            ->with('port')
            ->get();

        return response()->json([
            'data' => BerthResource::collection($berths),
        ]);
    }

    /**
     * Latest 6 active berths by created_at.
     */
    public function latest(): JsonResponse
    {
        $berths = Berth::query()
            ->active()
            ->orderByDesc('created_at')
            ->limit(6)
            ->with('port')
            ->get();

        return response()->json([
            'data' => BerthResource::collection($berths),
        ]);
    }
}
