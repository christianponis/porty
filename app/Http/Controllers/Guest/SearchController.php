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
            ->with(['port', 'owner'])
            ->active()
            ->available()
            ->when($request->port_id, fn($q, $id) => $q->where('port_id', $id))
            ->when($request->region, fn($q, $r) => $q->whereHas('port', fn($pq) => $pq->where('region', $r)))
            ->when($request->city, fn($q, $c) => $q->whereHas('port', fn($pq) => $pq->where('city', 'like', "%{$c}%")))
            ->when($request->min_length, fn($q, $l) => $q->where('length_m', '>=', $l))
            ->when($request->max_price, fn($q, $p) => $q->where('price_per_day', '<=', $p))
            ->orderBy('price_per_day')
            ->paginate(12);

        $regions = \App\Models\Port::active()
            ->whereNotNull('region')
            ->distinct()
            ->pluck('region')
            ->sort();

        return view('guest.search', compact('berths', 'regions'));
    }

    public function show(Berth $berth)
    {
        $berth->load(['port', 'owner', 'availabilities']);

        return view('guest.berth-detail', compact('berth'));
    }
}
