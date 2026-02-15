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
