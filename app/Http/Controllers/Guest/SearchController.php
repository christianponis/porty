<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Berth;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $berths = Berth::query()
            ->with(['port', 'owner'])->withCount('reviews')
            ->active()
            ->available()
            ->when($request->port_id, fn($q, $id) => $q->where('port_id', $id))
            ->when($request->country, fn($q, $c) => $q->whereHas('port', fn($pq) => $pq->where('country', $c)))
            ->when($request->region, fn($q, $r) => $q->whereHas('port', fn($pq) => $pq->where('region', $r)))
            ->when($request->min_length, fn($q, $l) => $q->where('length_m', '>=', $l))
            ->when($request->min_width, fn($q, $w) => $q->where('width_m', '>=', $w))
            ->when($request->max_price, fn($q, $p) => $q->where('price_per_day', '<=', $p))
            ->when($request->min_anchors, fn($q, $a) => $q->where(function($q2) use ($a) {
                $q2->where('blue_anchor_count', '>=', $a)
                    ->orWhere('grey_anchor_count', '>=', $a);
            }))
            ->orderBy('price_per_day')
            ->paginate(12);

        $allPorts = \App\Models\Port::active()
            ->orderBy('country')
            ->orderBy('region')
            ->orderBy('name')
            ->get(['id', 'name', 'city', 'region', 'country']);

        $countries = $allPorts->pluck('country')->unique()->sort()->values();

        return view('guest.search', compact('berths', 'allPorts', 'countries'));
    }

    public function show(Berth $berth)
    {
        $berth->load(['port', 'owner', 'availabilities', 'reviews.guest', 'selfAssessment', 'latestCertification']);

        return view('guest.berth-detail', compact('berth'));
    }
}
