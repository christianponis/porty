<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Resources\PortResource;
use App\Models\Port;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PortController extends Controller
{
    /**
     * List ports with optional filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Port::query()
            ->active()
            ->whereHas('berths', function ($q) {
                $q->where('is_active', true);
            });

        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $ports = $query->orderBy('name')->paginate($request->integer('per_page', 15));

        return response()->json(PortResource::collection($ports)->response()->getData(true));
    }

    /**
     * Show a single port.
     */
    public function show(Port $port): JsonResponse
    {
        return response()->json([
            'data' => new PortResource($port),
        ]);
    }

    /**
     * Get distinct countries from active ports.
     */
    public function countries(): JsonResponse
    {
        $countries = Port::query()
            ->active()
            ->whereHas('berths', function ($q) {
                $q->where('is_active', true);
            })
            ->whereNotNull('country')
            ->distinct()
            ->orderBy('country')
            ->pluck('country');

        return response()->json([
            'data' => $countries,
        ]);
    }

    /**
     * Get distinct regions, optionally filtered by country.
     */
    public function regions(Request $request): JsonResponse
    {
        $query = Port::query()
            ->active()
            ->whereHas('berths', function ($q) {
                $q->where('is_active', true);
            })
            ->whereNotNull('region');

        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        $regions = $query->distinct()
            ->orderBy('region')
            ->pluck('region');

        return response()->json([
            'data' => $regions,
        ]);
    }
}
