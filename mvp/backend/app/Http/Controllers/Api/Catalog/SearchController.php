<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Resources\BerthResource;
use App\Models\Berth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Search berths with comprehensive filters.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $query = Berth::query()->active()->with('port');

        // Port filter
        if ($request->filled('port_id')) {
            $query->where('port_id', $request->port_id);
        }

        // Location filters via port relation
        if ($request->filled('country')) {
            $query->whereHas('port', function ($q) use ($request) {
                $q->where('country', $request->country);
            });
        }

        if ($request->filled('region')) {
            $query->whereHas('port', function ($q) use ($request) {
                $q->where('region', $request->region);
            });
        }

        // Date availability filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $dateFrom = $request->date_from;
            $dateTo = $request->date_to;

            $query->whereHas('availabilities', function ($q) use ($dateFrom, $dateTo) {
                $q->where('is_available', true)
                  ->where('start_date', '<=', $dateFrom)
                  ->where('end_date', '>=', $dateTo);
            });

            // Exclude berths with conflicting bookings
            $query->whereDoesntHave('bookings', function ($q) use ($dateFrom, $dateTo) {
                $q->whereIn('status', ['pending', 'confirmed'])
                  ->where('start_date', '<', $dateTo)
                  ->where('end_date', '>', $dateFrom);
            });
        }

        // Dimension filters
        if ($request->filled('min_length')) {
            $query->where('length_m', '>=', $request->min_length);
        }
        if ($request->filled('max_length')) {
            $query->where('length_m', '<=', $request->max_length);
        }
        if ($request->filled('min_width')) {
            $query->where('width_m', '>=', $request->min_width);
        }
        if ($request->filled('max_width')) {
            $query->where('width_m', '<=', $request->max_width);
        }
        if ($request->filled('min_depth')) {
            $query->where('max_draft_m', '>=', $request->min_depth);
        }
        if ($request->filled('max_depth')) {
            $query->where('max_draft_m', '<=', $request->max_depth);
        }

        // Price filters
        if ($request->filled('min_price')) {
            $query->where('price_per_day', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price_per_day', '<=', $request->max_price);
        }

        // Rating filter
        if ($request->filled('min_rating')) {
            $query->where('review_average', '>=', $request->min_rating);
        }

        // Sharing filter
        if ($request->has('sharing_enabled')) {
            $query->where('sharing_enabled', (bool) $request->sharing_enabled);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'newest');
        match ($sortBy) {
            'price_asc' => $query->orderBy('price_per_day', 'asc'),
            'price_desc' => $query->orderBy('price_per_day', 'desc'),
            'rating_desc' => $query->orderByDesc('review_average'),
            default => $query->orderByDesc('created_at'), // newest
        };

        $berths = $query->paginate(12);

        return response()->json(BerthResource::collection($berths)->response()->getData(true));
    }
}
