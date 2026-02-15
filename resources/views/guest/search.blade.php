<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-slate-900 leading-tight">Cerca un posto barca</h2>
            <p class="text-sm text-slate-500 mt-1">Trova il posto barca ideale nei porti italiani</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Filtri --}}
            <div class="card card-body mb-8 lg:sticky lg:top-20 lg:z-10">
                <form method="GET" action="{{ route('search') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <x-input-label value="Regione" />
                        <select name="region" class="form-select mt-1 block w-full">
                            <option value="">Tutte</option>
                            @foreach($regions as $region)
                                <option value="{{ $region }}" {{ request('region') === $region ? 'selected' : '' }}>{{ $region }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Citta" />
                        <x-text-input type="text" name="city" :value="request('city')" class="mt-1 block w-full" placeholder="es. Rimini" />
                    </div>
                    <div>
                        <x-input-label value="Lunghezza min (m)" />
                        <x-text-input type="number" name="min_length" :value="request('min_length')" class="mt-1 block w-full" step="0.5" min="1" />
                    </div>
                    <div>
                        <x-input-label value="Prezzo max/giorno" />
                        <x-text-input type="number" name="max_price" :value="request('max_price')" class="mt-1 block w-full" step="5" min="1" />
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-primary w-full justify-center inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Cerca
                        </button>
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
                            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-ocean-500 to-ocean-600"></div>
                            <div class="p-6 pt-5">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="text-lg font-bold text-slate-900 group-hover:text-ocean-700 transition-colors">{{ $berth->title }}</h3>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-ocean-50 text-ocean-700 ring-1 ring-inset ring-ocean-500/20">
                                        {{ $berth->code }}
                                    </span>
                                </div>
                                <p class="text-sm text-slate-500 mb-4">{{ $berth->port->name }} - {{ $berth->port->city }}</p>

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
