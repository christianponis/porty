<?php

namespace App\Http\Controllers\Api\Booking;

use App\Http\Controllers\Controller;
use App\Models\Berth;
use App\Models\BerthAvailability;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    /**
     * Get availability periods for a berth (for owners).
     */
    public function index(Berth $berth): JsonResponse
    {
        $user = auth('api')->user();

        if ($berth->owner_id !== $user->id && ! $user->isAdmin()) {
            return response()->json([
                'message' => 'Non autorizzato.',
            ], 403);
        }

        $availabilities = $berth->availabilities()
            ->orderBy('start_date')
            ->get();

        return response()->json([
            'data' => $availabilities->map(function ($availability) {
                return [
                    'id' => $availability->id,
                    'start_date' => $availability->start_date->toDateString(),
                    'end_date' => $availability->end_date->toDateString(),
                    'is_available' => $availability->is_available,
                    'note' => $availability->note,
                ];
            }),
        ]);
    }

    /**
     * Create or update availability periods for a berth (for owners).
     */
    public function store(Request $request, Berth $berth): JsonResponse
    {
        $user = auth('api')->user();

        if ($berth->owner_id !== $user->id && ! $user->isAdmin()) {
            return response()->json([
                'message' => 'Non autorizzato.',
            ], 403);
        }

        $request->validate([
            'periods' => ['required', 'array', 'min:1'],
            'periods.*.start_date' => ['required', 'date'],
            'periods.*.end_date' => ['required', 'date', 'after:periods.*.start_date'],
            'periods.*.is_available' => ['required', 'boolean'],
            'periods.*.note' => ['nullable', 'string', 'max:500'],
        ]);

        $createdPeriods = [];

        foreach ($request->periods as $period) {
            $createdPeriods[] = BerthAvailability::updateOrCreate(
                [
                    'berth_id' => $berth->id,
                    'start_date' => $period['start_date'],
                    'end_date' => $period['end_date'],
                ],
                [
                    'is_available' => $period['is_available'],
                    'note' => $period['note'] ?? null,
                ]
            );
        }

        return response()->json([
            'message' => 'Disponibilità aggiornata con successo.',
            'data' => collect($createdPeriods)->map(function ($availability) {
                return [
                    'id' => $availability->id,
                    'start_date' => $availability->start_date->toDateString(),
                    'end_date' => $availability->end_date->toDateString(),
                    'is_available' => $availability->is_available,
                    'note' => $availability->note,
                ];
            }),
        ], 201);
    }
}
