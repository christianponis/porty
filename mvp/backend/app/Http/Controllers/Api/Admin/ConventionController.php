<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\ConventionCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConventionRequest;
use App\Http\Requests\UpdateConventionRequest;
use App\Http\Resources\ConventionResource;
use App\Models\Port;
use App\Models\PortConvention;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConventionController extends Controller
{
    /**
     * List all conventions with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = PortConvention::with('port');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('port_id')) {
            $query->where('port_id', $request->port_id);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        $conventions = $query->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        return response()->json(
            ConventionResource::collection($conventions)->response()->getData(true)
        );
    }

    /**
     * List convention categories.
     */
    public function categories(): JsonResponse
    {
        $categories = collect(ConventionCategory::cases())->map(fn ($c) => [
            'value' => $c->value,
            'label' => $c->label(),
        ]);

        return response()->json($categories);
    }

    /**
     * Show a single convention.
     */
    public function show(PortConvention $convention): JsonResponse
    {
        $convention->load('port');

        return response()->json([
            'data' => new ConventionResource($convention),
        ]);
    }

    /**
     * Create a new convention.
     */
    public function store(StoreConventionRequest $request): JsonResponse
    {
        $convention = PortConvention::create($request->validated());
        $convention->load('port');

        return response()->json([
            'message' => 'Convenzione creata con successo.',
            'data' => new ConventionResource($convention),
        ], 201);
    }

    /**
     * Update a convention.
     */
    public function update(UpdateConventionRequest $request, PortConvention $convention): JsonResponse
    {
        $convention->update($request->validated());
        $convention->load('port');

        return response()->json([
            'message' => 'Convenzione aggiornata con successo.',
            'data' => new ConventionResource($convention->fresh('port')),
        ]);
    }

    /**
     * Delete a convention.
     */
    public function destroy(PortConvention $convention): JsonResponse
    {
        $convention->delete();

        return response()->json([
            'message' => 'Convenzione eliminata con successo.',
        ]);
    }

    /**
     * Get conventions for a specific port.
     */
    public function byPort(Port $port): JsonResponse
    {
        $conventions = $port->conventions()
            ->orderBy('sort_order')
            ->get();

        return response()->json(ConventionResource::collection($conventions));
    }
}
