<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berth;
use App\Models\Certification;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index(Request $request)
    {
        $berths = Berth::query()
            ->with(['port', 'owner'])
            ->when($request->search, fn($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->rating_level, fn($q, $r) => $q->where('rating_level', $r))
            ->latest()
            ->paginate(20);

        return view('admin.ratings.index', compact('berths'));
    }

    public function certifications(Request $request)
    {
        $certifications = Certification::query()
            ->with(['berth.port', 'berth.owner'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20);

        return view('admin.ratings.certifications', compact('certifications'));
    }
}
