<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Berth;
use App\Models\BerthAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailabilityController extends Controller
{
    public function store(Request $request, Berth $berth)
    {
        $this->authorizeOwner($berth);

        $request->validate([
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'note' => 'nullable|string|max:255',
        ]);

        $berth->availabilities()->create([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_available' => true,
            'note' => $request->note,
        ]);

        return back()->with('success', 'Periodo di disponibilita aggiunto.');
    }

    public function destroy(Berth $berth, BerthAvailability $availability)
    {
        $this->authorizeOwner($berth);

        if ($availability->berth_id !== $berth->id) {
            abort(404);
        }

        $availability->delete();

        return back()->with('success', 'Periodo di disponibilita rimosso.');
    }

    private function authorizeOwner(Berth $berth): void
    {
        if ($berth->owner_id !== Auth::id()) {
            abort(403, 'Non autorizzato.');
        }
    }
}
