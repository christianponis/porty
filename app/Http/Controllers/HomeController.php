<?php

namespace App\Http\Controllers;

use App\Models\Berth;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke()
    {
        $topBerths = Berth::query()
            ->with(['port', 'owner'])
            ->active()
            ->available()
            ->where(function ($q) {
                $q->whereNotNull('review_average')
                    ->orWhereNotNull('grey_anchor_count');
            })
            ->orderByRaw('COALESCE(review_average, 0) * 10 + COALESCE(review_count, 0) * 2 + COALESCE(blue_anchor_count, grey_anchor_count, 0) * 5 DESC')
            ->take(config('porty.homepage.top_berths_count', 6))
            ->get();

        $latestBerths = Berth::query()
            ->with(['port', 'owner'])
            ->active()
            ->available()
            ->latest()
            ->take(config('porty.homepage.latest_berths_count', 6))
            ->get();

        return view('welcome', compact('topBerths', 'latestBerths'));
    }
}
