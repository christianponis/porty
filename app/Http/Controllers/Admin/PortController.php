<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Port;
use Illuminate\Http\Request;

class PortController extends Controller
{
    public function index(Request $request)
    {
        $ports = Port::query()
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('city', 'like', "%{$s}%"))
            ->when($request->region, fn($q, $r) => $q->where('region', $r))
            ->withCount('berths')
            ->latest()
            ->paginate(20);

        return view('admin.ports.index', compact('ports'));
    }

    public function create()
    {
        return view('admin.ports.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'image_url' => 'nullable|url|max:255',
        ]);

        Port::create($validated);

        return redirect()->route('admin.ports')->with('success', 'Porto creato con successo.');
    }

    public function edit(Port $port)
    {
        return view('admin.ports.edit', compact('port'));
    }

    public function update(Request $request, Port $port)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'image_url' => 'nullable|url|max:255',
        ]);

        $port->update($validated);

        return redirect()->route('admin.ports')->with('success', 'Porto aggiornato con successo.');
    }
}
