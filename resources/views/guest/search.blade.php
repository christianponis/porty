<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-slate-900 leading-tight">Cerca un posto barca</h2>
            <p class="text-sm text-slate-500 mt-1">Trova il posto barca ideale nei porti del Mediterraneo</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Filtri --}}
            <div class="card card-body mb-8 lg:sticky lg:top-20 lg:z-10"
                 x-data="{
                    country: '{{ request('country', '') }}',
                    region: '{{ request('region', '') }}',
                    allPorts: {{ Js::from($allPorts->groupBy('country')->map(fn($g) => $g->groupBy('region'))) }},
                    get regions() {
                        if (!this.country || !this.allPorts[this.country]) return [];
                        return Object.keys(this.allPorts[this.country]).sort();
                    },
                    get filteredPorts() {
                        if (!this.country) return Object.values(this.allPorts).flatMap(r => Object.values(r).flat());
                        if (!this.region) return Object.values(this.allPorts[this.country] || {}).flat();
                        return (this.allPorts[this.country] || {})[this.region] || [];
                    }
                 }">
                <form method="GET" action="{{ route('search') }}" class="space-y-4">
                    {{-- Riga 1: Localizzazione --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-input-label value="Nazione" />
                            <select name="country" x-model="country" @change="region = ''" class="form-select mt-1 block w-full">
                                <option value="">Tutte</option>
                                @foreach($countries as $c)
                                    <option value="{{ $c }}" {{ request('country') === $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label value="Regione" />
                            <select name="region" x-model="region" class="form-select mt-1 block w-full" :disabled="!country">
                                <option value="">Tutte</option>
                                <template x-for="r in regions" :key="r">
                                    <option :value="r" x-text="r" :selected="r === '{{ request('region', '') }}'"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <x-input-label value="Porto" />
                            <select name="port_id" class="form-select mt-1 block w-full">
                                <option value="">Tutti</option>
                                <template x-for="port in filteredPorts" :key="port.id">
                                    <option :value="port.id" x-text="port.name + ' - ' + port.city" :selected="port.id == {{ request('port_id', 0) }}"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    {{-- Riga 2: Dimensioni, prezzo, ancore + bottone --}}
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div>
                            <x-input-label value="Lunghezza min (m)" />
                            <x-text-input type="number" name="min_length" :value="request('min_length')" class="mt-1 block w-full" step="0.5" min="1" />
                        </div>
                        <div>
                            <x-input-label value="Larghezza min (m)" />
                            <x-text-input type="number" name="min_width" :value="request('min_width')" class="mt-1 block w-full" step="0.5" min="1" />
                        </div>
                        <div>
                            <x-input-label value="Prezzo max/giorno" />
                            <x-text-input type="number" name="max_price" :value="request('max_price')" class="mt-1 block w-full" step="5" min="1" />
                        </div>
                        <div>
                            <x-input-label value="Min Ancore" />
                            <select name="min_anchors" class="form-select mt-1 block w-full">
                                <option value="">Tutte</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ request('min_anchors') == $i ? 'selected' : '' }}>{{ $i }}+</option>
                                @endfor
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="btn-primary w-full justify-center inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                Cerca
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Risultati --}}
            @if($berths->isEmpty())
                <div class="text-center py-16">
                    <div class="w-20 h-20 rounded-2xl bg-ocean-50 flex items-center justify-center mx-auto mb-5">
                        <svg class="w-10 h-10 text-ocean-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <p class="text-slate-900 text-lg font-semibold mb-2">Nessun posto barca trovato</p>
                    <p class="text-slate-500">Prova a modificare i filtri di ricerca.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($berths as $berth)
                        <div class="card group hover:shadow-card-hover hover:-translate-y-1 transition-all duration-300 overflow-hidden relative">
                            @if($berth->port->image_url)
                                <img src="{{ $berth->port->image_url }}" alt="{{ $berth->port->name }}" style="height:192px" class="w-full object-cover" loading="lazy">
                            @else
                                <div style="height:192px" class="bg-gradient-to-br from-ocean-100 via-ocean-50 to-slate-100 flex items-center justify-center">
                                    <svg class="w-10 h-10 text-ocean-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9l2-2m0 0l7-7 7 7M5 7v11a2 2 0 002 2h10a2 2 0 002-2V7"/></svg>
                                </div>
                            @endif
                            <div class="p-6 pt-4">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="text-lg font-bold text-slate-900 group-hover:text-ocean-700 transition-colors">{{ $berth->title }}</h3>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-ocean-50 text-ocean-700 ring-1 ring-inset ring-ocean-500/20">
                                        {{ $berth->code }}
                                    </span>
                                </div>
                                <p class="text-sm text-slate-500 mb-4">{{ $berth->port->name }} - {{ $berth->port->city }}</p>

                                @if($berth->getEffectiveAnchorCount() > 0 || $berth->review_count > 0)
                                    <div class="flex items-center gap-2 mb-3">
                                        @if($berth->getEffectiveAnchorCount() > 0)
                                            <x-anchor-rating :count="$berth->getEffectiveAnchorCount()" :level="$berth->getEffectiveRatingLevel()->value" size="sm" />
                                        @endif
                                        @if($berth->review_count > 0)
                                            <x-review-stars :rating="$berth->review_average" :count="$berth->review_count" size="sm" />
                                        @endif
                                    </div>
                                @endif

                                <div class="flex gap-2 mb-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-50 text-xs font-medium text-slate-600">
                                        {{ $berth->length_m }}m x {{ $berth->width_m }}m
                                    </span>
                                    @if($berth->max_draft_m)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-50 text-xs font-medium text-slate-600">
                                            ↓ {{ $berth->max_draft_m }}m
                                        </span>
                                    @endif
                                </div>

                                @if($berth->amenities)
                                    <div class="flex flex-wrap gap-1.5 mb-4">
                                        @foreach($berth->amenities as $amenity)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-seafoam-50 text-seafoam-700 ring-1 ring-inset ring-seafoam-500/20">{{ $amenity }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="flex justify-between items-center pt-4 border-t border-slate-100">
                                    <div>
                                        <span class="text-2xl font-bold text-ocean-600">&euro; {{ number_format($berth->price_per_day, 0, ',', '.') }}</span>
                                        <span class="text-sm text-slate-400">/giorno</span>
                                    </div>
                                    <a href="{{ route('berths.show', $berth) }}" class="btn-primary text-sm inline-flex items-center gap-1.5">
                                        Dettagli
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $berths->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
