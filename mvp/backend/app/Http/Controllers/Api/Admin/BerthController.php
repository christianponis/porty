<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berth;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BerthController extends Controller
{
    /**
     * Paginate all berths with filters (admin).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Berth::with(['port']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('port_id')) {
            $query->where('port_id', $request->port_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('rating_level')) {
            $query->where('rating_level', $request->rating_level);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        $berths = $query->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        // Attach owner info (cross-database)
        $ownerIds = $berths->pluck('owner_id')->unique()->filter();
        $owners = User::whereIn('id', $ownerIds)->get()->keyBy('id');

        $data = $berths->through(function ($berth) use ($owners) {
            $owner = $owners->get($berth->owner_id);
            return [
                'id' => $berth->id,
                'code' => $berth->code,
                'title' => $berth->title,
                'port' => $berth->port ? [
                    'id' => $berth->port->id,
                    'name' => $berth->port->name,
                    'city' => $berth->port->city,
                ] : null,
                'owner' => $owner ? [
                    'id' => $owner->id,
                    'name' => $owner->name,
                    'email' => $owner->email,
                ] : null,
                'length_m' => (float) $berth->length_m,
                'width_m' => (float) $berth->width_m,
                'price_per_day' => (float) $berth->price_per_day,
                'status' => $berth->status?->value ?? $berth->status,
                'is_active' => $berth->is_active,
                'rating_level' => $berth->rating_level?->value,
                'review_count' => $berth->review_count ?? 0,
                'review_average' => $berth->review_average ? (float) $berth->review_average : null,
                'sharing_enabled' => $berth->sharing_enabled,
                'created_at' => $berth->created_at?->toISOString(),
            ];
        });

        return response()->json($data);
    }

    /**
     * Show berth detail.
     */
    public function show(Berth $berth): JsonResponse
    {
        $berth->load(['port', 'reviews', 'selfAssessment', 'latestCertification']);

        $owner = User::find($berth->owner_id);

        return response()->json([
            'data' => [
                'id' => $berth->id,
                'code' => $berth->code,
                'title' => $berth->title,
                'description' => $berth->description,
                'port' => $berth->port ? [
                    'id' => $berth->port->id,
                    'name' => $berth->port->name,
                    'city' => $berth->port->city,
                    'region' => $berth->port->region,
                    'country' => $berth->port->country,
                ] : null,
                'owner' => $owner ? [
                    'id' => $owner->id,
                    'name' => $owner->name,
                    'email' => $owner->email,
                ] : null,
                'length_m' => (float) $berth->length_m,
                'width_m' => (float) $berth->width_m,
                'max_draft_m' => (float) $berth->max_draft_m,
                'price_per_day' => (float) $berth->price_per_day,
                'price_per_week' => $berth->price_per_week ? (float) $berth->price_per_week : null,
                'price_per_month' => $berth->price_per_month ? (float) $berth->price_per_month : null,
                'amenities' => $berth->amenities ?? [],
                'images' => $berth->images ?? [],
                'status' => $berth->status?->value ?? $berth->status,
                'is_active' => $berth->is_active,
                'rating_level' => $berth->rating_level?->value,
                'grey_anchor_count' => $berth->grey_anchor_count,
                'blue_anchor_count' => $berth->blue_anchor_count,
                'gold_anchor_count' => $berth->gold_anchor_count,
                'review_count' => $berth->review_count ?? 0,
                'review_average' => $berth->review_average ? (float) $berth->review_average : null,
                'sharing_enabled' => $berth->sharing_enabled,
                'nodi_value_per_day' => $berth->nodi_value_per_day ? (float) $berth->nodi_value_per_day : null,
                'total_bookings' => $berth->bookings()->count(),
                'total_revenue' => (float) $berth->bookings()->where('status', 'completed')->sum('total_price'),
                'created_at' => $berth->created_at?->toISOString(),
            ],
        ]);
    }

    /**
     * Toggle berth active status.
     */
    public function toggleActive(Berth $berth): JsonResponse
    {
        $berth->update(['is_active' => !$berth->is_active]);

        return response()->json([
            'message' => $berth->is_active ? 'Posto barca attivato.' : 'Posto barca disattivato.',
            'is_active' => $berth->is_active,
        ]);
    }
}
