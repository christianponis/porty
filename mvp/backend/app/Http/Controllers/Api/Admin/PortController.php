<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PortResource;
use App\Models\Port;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PortController extends Controller
{
    /**
     * Paginate all ports (admin).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Port::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        $ports = $query->orderBy('name')
            ->paginate($request->integer('per_page', 15));

        return response()->json(PortResource::collection($ports)->response()->getData(true));
    }

    /**
     * Show a single port with counts.
     */
    public function show(Port $port): JsonResponse
    {
        $port->loadCount(['berths']);

        return response()->json([
            'data' => array_merge(
                (new PortResource($port))->toArray(request()),
                [
                    'berths_count' => $port->berths_count ?? 0,
                    'active_berths_count' => $port->berths()->where('is_active', true)->count(),
                ]
            ),
        ]);
    }

    /**
     * Create a new port.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'region' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'address' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'amenities' => ['nullable', 'array'],
            'is_active' => ['boolean'],
        ]);

        $port = Port::create($request->all());

        return response()->json([
            'message' => 'Porto creato con successo.',
            'data' => new PortResource($port),
        ], 201);
    }

    /**
     * Update an existing port.
     */
    public function update(Request $request, Port $port): JsonResponse
    {
        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'city' => ['sometimes', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'region' => ['sometimes', 'string', 'max:255'],
            'country' => ['sometimes', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'address' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'amenities' => ['nullable', 'array'],
            'is_active' => ['boolean'],
        ]);

        $port->update($request->all());

        return response()->json([
            'message' => 'Porto aggiornato con successo.',
            'data' => new PortResource($port->fresh()),
        ]);
    }
}
