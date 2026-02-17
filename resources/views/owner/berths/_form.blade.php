@php $berth = $berth ?? null; @endphp

<div>
    <x-input-label for="port_id" value="Porto *" />
    <select id="port_id" name="port_id" class="form-select mt-1 block w-full" required>
        <option value="">Seleziona un porto...</option>
        @foreach($ports as $port)
            <option value="{{ $port->id }}" {{ old('port_id', $berth?->port_id) == $port->id ? 'selected' : '' }}>
                {{ $port->name }} - {{ $port->city }} ({{ $port->province }})
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('port_id')" class="mt-2" />
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
    <div>
        <x-input-label for="code" value="Codice posto *" />
        <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code', $berth?->code)" placeholder="es. A-15" required />
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="title" value="Titolo annuncio *" />
        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $berth?->title)" required />
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>
</div>

<div class="mt-4">
    <x-input-label for="description" value="Descrizione" />
    <textarea id="description" name="description" class="form-textarea mt-1 block w-full" rows="3">{{ old('description', $berth?->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
    <div>
        <x-input-label for="length_m" value="Lunghezza (m) *" />
        <x-text-input id="length_m" name="length_m" type="number" step="0.01" min="1" class="mt-1 block w-full" :value="old('length_m', $berth?->length_m)" required />
        <x-input-error :messages="$errors->get('length_m')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="width_m" value="Larghezza (m) *" />
        <x-text-input id="width_m" name="width_m" type="number" step="0.01" min="1" class="mt-1 block w-full" :value="old('width_m', $berth?->width_m)" required />
        <x-input-error :messages="$errors->get('width_m')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="max_draft_m" value="Pescaggio max (m)" />
        <x-text-input id="max_draft_m" name="max_draft_m" type="number" step="0.01" min="0.5" class="mt-1 block w-full" :value="old('max_draft_m', $berth?->max_draft_m)" />
        <x-input-error :messages="$errors->get('max_draft_m')" class="mt-2" />
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
    <div>
        <x-input-label for="price_per_day" value="Prezzo/giorno (&euro;) *" />
        <x-text-input id="price_per_day" name="price_per_day" type="number" step="0.01" min="1" class="mt-1 block w-full" :value="old('price_per_day', $berth?->price_per_day)" required />
        <x-input-error :messages="$errors->get('price_per_day')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="price_per_week" value="Prezzo/settimana (&euro;)" />
        <x-text-input id="price_per_week" name="price_per_week" type="number" step="0.01" min="1" class="mt-1 block w-full" :value="old('price_per_week', $berth?->price_per_week)" />
        <x-input-error :messages="$errors->get('price_per_week')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="price_per_month" value="Prezzo/mese (&euro;)" />
        <x-text-input id="price_per_month" name="price_per_month" type="number" step="0.01" min="1" class="mt-1 block w-full" :value="old('price_per_month', $berth?->price_per_month)" />
        <x-input-error :messages="$errors->get('price_per_month')" class="mt-2" />
    </div>
</div>

<div class="mt-4">
    <x-input-label value="Servizi inclusi" />
    <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-2">
        @php
            $allAmenities = ['electricity', 'water', 'wifi', 'security', 'concierge'];
            $currentAmenities = old('amenities', $berth?->amenities ?? []);
        @endphp
        @foreach($allAmenities as $amenity)
            <label class="inline-flex items-center">
                <input type="checkbox" name="amenities[]" value="{{ $amenity }}" class="rounded border-slate-300 text-ocean-600 shadow-sm focus:ring-ocean-500"
                    {{ in_array($amenity, $currentAmenities) ? 'checked' : '' }}>
                <span class="ms-2 text-sm text-slate-600">{{ ucfirst($amenity) }}</span>
            </label>
        @endforeach
    </div>
</div>

@if(!$berth)
<div class="mt-6 p-4 bg-ocean-50/50 rounded-xl">
    <h4 class="text-sm font-semibold text-ocean-800 mb-3">Periodo di disponibilita iniziale</h4>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <x-input-label for="availability_start" value="Data inizio" />
            <x-text-input id="availability_start" name="availability_start" type="date" class="mt-1 block w-full" :value="old('availability_start')" />
        </div>
        <div>
            <x-input-label for="availability_end" value="Data fine" />
            <x-text-input id="availability_end" name="availability_end" type="date" class="mt-1 block w-full" :value="old('availability_end')" />
        </div>
    </div>
</div>
@endif

{{-- Sharing --}}
<div class="mt-6 p-4 bg-seafoam-50/50 rounded-xl border border-seafoam-200/50">
    <h4 class="text-sm font-semibold text-seafoam-800 mb-3 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="16" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 400 400"><path d="M24 219.943C73.8113 211.642 120.446 207.312 169.006 155.742"/><path d="M199.206 130.337C250.916 99.842 326.254 151.462 318.438 211.641C310.211 274.983 221.728 286.903 176.199 281.931C130.669 276.958 109.033 266.778 88.6484 233.191"/><path d="M27.875 242.635C92.0763 233.78 144.795 214.431 190.039 169.025"/><path d="M221.711 148.426C250.633 136.713 322.466 192.991 283.323 225.705C235.939 265.308 146.315 263.113 119.195 227.691"/><path d="M79.9414 189.261C82.332 133.37 147.283 104.175 182.528 122.421C216.417 139.967 245.676 179.692 289.891 197.842"/><path d="M319.883 205.305C347.282 210.877 363.254 212.284 375.998 206.857"/><path d="M104.094 184.721C132.549 119.68 181.475 149.902 198.771 163.673C216.068 177.444 258.788 215.116 282.202 219.8"/><path d="M315.047 228.935C328.797 233.688 357.329 236.533 376 230.429"/></svg>
        Sharing (Nodi)
    </h4>
    <div class="space-y-4">
        <label class="inline-flex items-center">
            <input type="checkbox" name="sharing_enabled" value="1" class="rounded border-slate-300 text-seafoam-600 shadow-sm focus:ring-seafoam-500"
                {{ old('sharing_enabled', $berth?->sharing_enabled) ? 'checked' : '' }}>
            <span class="ms-2 text-sm text-slate-700 font-medium">Abilita sharing con Nodi</span>
        </label>

        <div>
            <x-input-label for="nodi_value_per_day" value="Valore Nodi al giorno" />
            <x-text-input id="nodi_value_per_day" name="nodi_value_per_day" type="number" step="0.01" min="1"
                class="mt-1 block w-full" :value="old('nodi_value_per_day', $berth?->nodi_value_per_day)"
                placeholder="es. 10.00" />
            <p class="text-xs text-slate-400 mt-1">Il valore base in Nodi per giorno di ormeggio. Viene moltiplicato per il rating delle ancore.</p>
            <x-input-error :messages="$errors->get('nodi_value_per_day')" class="mt-2" />
        </div>
    </div>
</div>
