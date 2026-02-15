<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBerthRequest;
use App\Http\Requests\UpdateBerthRequest;
use App\Models\Berth;
use App\Models\BerthAvailability;
use App\Models\Port;
use Illuminate\Support\Facades\Auth;

class BerthController extends Controller
{
    public function index()
    {
        $berths = Auth::user()->berths()
            ->with('port')
            ->withCount('bookings')
            ->latest()
            ->paginate(10);

        return view('owner.berths.index', compact('berths'));
    }

    public function create()
    {
        $ports = Port::active()->orderBy('name')->get();
        return view('owner.berths.create', compact('ports'));
    }

    public function store(StoreBerthRequest $request)
    {
        $validated = $request->validated();

        $berthData = collect($validated)->except(['availability_start', 'availability_end'])->toArray();
        $berth = Auth::user()->berths()->create($berthData);

        if ($request->availability_start && $request->availability_end) {
            BerthAvailability::create([
                'berth_id' => $berth->id,
                'start_date' => $request->availability_start,
                'end_date' => $request->availability_end,
                'is_available' => true,
            ]);
        }

        return redirect()->route('owner.berths.index')->with('success', 'Posto barca creato con successo.');
    }

    public function show(Berth $berth)
    {
        $this->authorizeOwner($berth);
        $berth->load(['port', 'availabilities', 'bookings.guest']);

        return view('owner.berths.show', compact('berth'));
    }

    public function edit(Berth $berth)
    {
        $this->authorizeOwner($berth);
        $ports = Port::active()->orderBy('name')->get();

        return view('owner.berths.edit', compact('berth', 'ports'));
    }

    public function update(UpdateBerthRequest $request, Berth $berth)
    {
        $this->authorizeOwner($berth);
        $berth->update($request->validated());

        return redirect()->route('owner.berths.index')->with('success', 'Posto barca aggiornato.');
    }

    public function destroy(Berth $berth)
    {
        $this->authorizeOwner($berth);
        $berth->update(['is_active' => false]);

        return redirect()->route('owner.berths.index')->with('success', 'Posto barca disattivato.');
    }

    private function authorizeOwner(Berth $berth): void
    {
        if ($berth->owner_id !== Auth::id()) {
            abort(403, 'Non autorizzato.');
        }
    }
}
