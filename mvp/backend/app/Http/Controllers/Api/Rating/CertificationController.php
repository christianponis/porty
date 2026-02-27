<?php

namespace App\Http\Controllers\Api\Rating;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use Illuminate\Http\JsonResponse;

class CertificationController extends Controller
{
    /**
     * List all certifications (admin only).
     */
    public function index(): JsonResponse
    {
        $certifications = Certification::with(['berth.port', 'inspector'])
            ->orderByDesc('created_at')
            ->paginate(15);

        $data = $certifications->through(function ($cert) {
            return [
                'id' => $cert->id,
                'berth' => $cert->berth ? [
                    'id' => $cert->berth->id,
                    'title' => $cert->berth->title,
                    'port' => $cert->berth->port ? [
                        'id' => $cert->berth->port->id,
                        'name' => $cert->berth->port->name,
                    ] : null,
                ] : null,
                'inspector' => $cert->inspector ? [
                    'id' => $cert->inspector->id,
                    'name' => $cert->inspector->name,
                ] : null,
                'status' => $cert->status?->value,
                'total_score' => $cert->total_score ? (float) $cert->total_score : null,
                'anchor_count' => $cert->anchor_count,
                'inspection_date' => $cert->inspection_date?->toDateString(),
                'valid_until' => $cert->valid_until?->toDateString(),
                'is_valid' => $cert->isValid(),
                'created_at' => $cert->created_at?->toISOString(),
            ];
        });

        return response()->json($data);
    }
}
