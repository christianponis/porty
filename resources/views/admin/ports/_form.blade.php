@php $port = $port ?? null; @endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label for="name" value="Nome porto *" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $port?->name)" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="city" value="Citta *" />
        <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $port?->city)" required />
        <x-input-error :messages="$errors->get('city')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="province" value="Provincia" />
        <x-text-input id="province" name="province" type="text" class="mt-1 block w-full" :value="old('province', $port?->province)" maxlength="10" />
        <x-input-error :messages="$errors->get('province')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="region" value="Regione" />
        <x-text-input id="region" name="region" type="text" class="mt-1 block w-full" :value="old('region', $port?->region)" />
        <x-input-error :messages="$errors->get('region')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="latitude" value="Latitudine" />
        <x-text-input id="latitude" name="latitude" type="number" step="0.0000001" class="mt-1 block w-full" :value="old('latitude', $port?->latitude)" />
        <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="longitude" value="Longitudine" />
        <x-text-input id="longitude" name="longitude" type="number" step="0.0000001" class="mt-1 block w-full" :value="old('longitude', $port?->longitude)" />
        <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
    </div>
</div>

<div class="mt-4">
    <x-input-label for="address" value="Indirizzo" />
    <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $port?->address)" />
    <x-input-error :messages="$errors->get('address')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="description" value="Descrizione" />
    <textarea id="description" name="description" class="form-textarea mt-1 block w-full" rows="3">{{ old('description', $port?->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label value="Servizi" />
    <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-2">
        @php
            $allAmenities = ['wifi', 'electricity', 'water', 'fuel', 'toilets', 'showers', 'restaurant', 'parking', 'security'];
            $currentAmenities = old('amenities', $port?->amenities ?? []);
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

<div class="mt-4">
    <x-input-label for="image_url" value="URL immagine" />
    <x-text-input id="image_url" name="image_url" type="url" class="mt-1 block w-full" :value="old('image_url', $port?->image_url)" placeholder="https://..." />
    <x-input-error :messages="$errors->get('image_url')" class="mt-2" />
</div>
