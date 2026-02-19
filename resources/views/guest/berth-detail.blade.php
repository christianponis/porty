<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('search') }}" class="text-slate-400 hover:text-ocean-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-bold text-xl text-slate-900 leading-tight">{{ $berth->title }}</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($berth->port->image_url)
                <div class="h-56 md:h-72 rounded-2xl overflow-hidden mb-8 shadow-lg">
                    <img src="{{ $berth->port->image_url }}" alt="{{ $berth->port->name }}" class="w-full h-full object-cover">
                </div>
            @endif
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- Colonna principale --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Info posto barca --}}
                    <div class="card card-body">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h1 class="text-2xl font-bold text-slate-900">{{ $berth->title }}</h1>
                                <p class="text-slate-500 mt-1">{{ $berth->port->name }} - {{ $berth->port->city }}, {{ $berth->port->province }}</p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-ocean-50 text-ocean-700 ring-1 ring-inset ring-ocean-500/20">
                                {{ $berth->code }}
                            </span>
                        </div>

                        @if($berth->getEffectiveAnchorCount() > 0)
                            <div class="flex items-center gap-3 mb-4">
                                <x-anchor-rating :count="$berth->getEffectiveAnchorCount()" :level="$berth->getEffectiveRatingLevel()->value" size="md" :showLabel="true" />
                                @if($berth->review_count > 0)
                                    <span class="text-slate-300">|</span>
                                    <x-review-stars :rating="$berth->review_average" :count="$berth->review_count" size="md" />
                                @endif
                            </div>
                        @endif

                        @if($berth->description)
                            <div class="mb-6">
                                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Descrizione</h3>
                                <p class="text-slate-700 leading-relaxed">{{ $berth->description }}</p>
                            </div>
                        @endif

                        {{-- Dimensioni --}}
                        <div class="mb-6">
                            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Dimensioni</h3>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="bg-ocean-50/50 rounded-xl p-4 text-center">
                                    <span class="block text-2xl font-bold text-ocean-700">{{ $berth->length_m }}</span>
                                    <span class="text-sm text-slate-500">Lunghezza (m)</span>
                                </div>
                                <div class="bg-ocean-50/50 rounded-xl p-4 text-center">
                                    <span class="block text-2xl font-bold text-ocean-700">{{ $berth->width_m }}</span>
                                    <span class="text-sm text-slate-500">Larghezza (m)</span>
                                </div>
                                <div class="bg-ocean-50/50 rounded-xl p-4 text-center">
                                    <span class="block text-2xl font-bold text-ocean-700">{{ $berth->max_draft_m ?? '-' }}</span>
                                    <span class="text-sm text-slate-500">Pescaggio max (m)</span>
                                </div>
                            </div>
                        </div>

                        {{-- Servizi --}}
                        @if($berth->amenities)
                            <div class="mb-6">
                                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Servizi disponibili</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($berth->amenities as $amenity)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm bg-seafoam-50 text-seafoam-700 ring-1 ring-inset ring-seafoam-500/20 font-medium">
                                            {{ $amenity }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Info porto --}}
                    <div class="card card-body">
                        <h3 class="text-lg font-bold text-slate-900 mb-4">Informazioni sul porto</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-slate-100 last:border-0">
                                <span class="text-slate-500">Porto</span>
                                <span class="font-medium text-slate-900">{{ $berth->port->name }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-100 last:border-0">
                                <span class="text-slate-500">Citta</span>
                                <span class="font-medium text-slate-900">{{ $berth->port->city }}, {{ $berth->port->province }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-100 last:border-0">
                                <span class="text-slate-500">Regione</span>
                                <span class="font-medium text-slate-900">{{ $berth->port->region }}</span>
                            </div>
                            @if($berth->port->address)
                                <div class="flex justify-between py-2 border-b border-slate-100 last:border-0">
                                    <span class="text-slate-500">Indirizzo</span>
                                    <span class="font-medium text-slate-900">{{ $berth->port->address }}</span>
                                </div>
                            @endif
                            @if($berth->port->amenities)
                                <div class="pt-3">
                                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-2">Servizi del porto</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($berth->port->amenities as $amenity)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-slate-100 text-slate-600">{{ $amenity }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Recensioni --}}
                    @if(isset($berth->reviews) && $berth->reviews->isNotEmpty())
                        <div class="card card-body">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold text-slate-900">Recensioni ({{ $berth->review_count }})</h3>
                                <x-review-stars :rating="$berth->review_average" :count="$berth->review_count" />
                            </div>
                            <div class="space-y-4">
                                @foreach($berth->reviews->take(5) as $review)
                                    <div class="border-b border-slate-100 last:border-0 pb-4 last:pb-0">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-full bg-ocean-100 flex items-center justify-center text-ocean-700 text-xs font-bold">
                                                    {{ strtoupper(substr($review->guest->name, 0, 2)) }}
                                                </div>
                                                <span class="text-sm font-semibold text-slate-900">{{ $review->guest->name }}</span>
                                                @if($review->is_verified)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-seafoam-50 text-seafoam-700">Verificata</span>
                                                @endif
                                            </div>
                                            <span class="text-xs text-slate-400">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                        <x-review-stars :rating="$review->average_rating" size="sm" />
                                        @if($review->comment)
                                            <p class="text-sm text-slate-600 mt-2">{{ $review->comment }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Disponibilita --}}
                    @if($berth->availabilities->isNotEmpty())
                        <div class="card card-body">
                            <h3 class="text-lg font-bold text-slate-900 mb-4">Periodi di disponibilita</h3>
                            <div class="space-y-3">
                                @foreach($berth->availabilities->where('is_available', true)->sortBy('start_date') as $availability)
                                    <div class="flex items-center justify-between bg-seafoam-50 rounded-xl p-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-2.5 h-2.5 rounded-full bg-seafoam-500"></div>
                                            <span class="text-sm font-medium text-slate-900">
                                                {{ \Carbon\Carbon::parse($availability->start_date)->format('d/m/Y') }}
                                                -
                                                {{ \Carbon\Carbon::parse($availability->end_date)->format('d/m/Y') }}
                                            </span>
                                        </div>
                                        @if($availability->note)
                                            <span class="text-xs text-slate-500">{{ $availability->note }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Sidebar: Prezzi e prenotazione --}}
                <div class="space-y-6">
                    {{-- Card prezzi --}}
                    <div class="card overflow-hidden sticky top-20">
                        <div class="ocean-gradient px-6 py-4">
                            <h3 class="text-lg font-bold text-white">Prezzi</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-600">Al giorno</span>
                                    <span class="text-2xl font-bold text-ocean-600">&euro; {{ number_format($berth->price_per_day, 2, ',', '.') }}</span>
                                </div>
                                @if($berth->price_per_week)
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">A settimana</span>
                                        <span class="text-lg font-semibold text-slate-900">&euro; {{ number_format($berth->price_per_week, 2, ',', '.') }}</span>
                                    </div>
                                @endif
                                @if($berth->price_per_month)
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">Al mese</span>
                                        <span class="text-lg font-semibold text-slate-900">&euro; {{ number_format($berth->price_per_month, 2, ',', '.') }}</span>
                                    </div>
                                @endif
                            </div>

                            @if($berth->sharing_enabled && $berth->nodi_value_per_day)
                                <div class="mt-4 pt-4 border-t border-slate-100">
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600 flex items-center gap-1">
                                            <svg class="w-4 h-4 text-seafoam-600" fill="none" stroke="currentColor" stroke-width="16" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 400 400"><path d="M24 219.943C73.8113 211.642 120.446 207.312 169.006 155.742"/><path d="M199.206 130.337C250.916 99.842 326.254 151.462 318.438 211.641C310.211 274.983 221.728 286.903 176.199 281.931C130.669 276.958 109.033 266.778 88.6484 233.191"/><path d="M27.875 242.635C92.0763 233.78 144.795 214.431 190.039 169.025"/><path d="M221.711 148.426C250.633 136.713 322.466 192.991 283.323 225.705C235.939 265.308 146.315 263.113 119.195 227.691"/><path d="M79.9414 189.261C82.332 133.37 147.283 104.175 182.528 122.421C216.417 139.967 245.676 179.692 289.891 197.842"/><path d="M319.883 205.305C347.282 210.877 363.254 212.284 375.998 206.857"/><path d="M104.094 184.721C132.549 119.68 181.475 149.902 198.771 163.673C216.068 177.444 258.788 215.116 282.202 219.8"/><path d="M315.047 228.935C328.797 233.688 357.329 236.533 376 230.429"/></svg>
                                            Nodi/giorno
                                        </span>
                                        <span class="text-lg font-bold text-seafoam-600">{{ number_format($berth->nodi_value_per_day, 0) }} Nodi</span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-1">Moltiplicatore: x{{ $berth->getNodiMultiplier() }}</p>
                                </div>
                            @endif

                            <div class="mt-6 pt-6 border-t border-slate-100">
                                @auth
                                    @if(auth()->user()->id !== $berth->owner_id)
                                        <form method="POST" action="{{ route('bookings.store') }}" id="booking-form">
                                            @csrf
                                            <input type="hidden" name="berth_id" value="{{ $berth->id }}">

                                            <div class="space-y-4">
                                                <div>
                                                    <x-input-label for="start_date" value="Data arrivo" />
                                                    <x-text-input type="date" id="start_date" name="start_date"
                                                        class="mt-1 block w-full" required
                                                        min="{{ now()->addDay()->format('Y-m-d') }}"
                                                        :value="old('start_date')" />
                                                    <x-input-error :messages="$errors->get('start_date')" class="mt-1" />
                                                </div>
                                                <div>
                                                    <x-input-label for="end_date" value="Data partenza" />
                                                    <x-text-input type="date" id="end_date" name="end_date"
                                                        class="mt-1 block w-full" required
                                                        min="{{ now()->addDays(2)->format('Y-m-d') }}"
                                                        :value="old('end_date')" />
                                                    <x-input-error :messages="$errors->get('end_date')" class="mt-1" />
                                                </div>

                                                {{-- Anteprima prezzo --}}
                                                <div id="price-preview" class="hidden bg-ocean-50 rounded-xl p-3.5">
                                                    <div class="flex justify-between text-sm">
                                                        <span class="text-slate-600"><span id="preview-days">0</span> giorni</span>
                                                        <span class="font-bold text-ocean-700" id="preview-price">&euro; 0,00</span>
                                                    </div>
                                                </div>

                                                <div>
                                                    <x-input-label for="guest_notes" value="Note (opzionale)" />
                                                    <textarea id="guest_notes" name="guest_notes" rows="2"
                                                        class="form-textarea mt-1 block w-full"
                                                        placeholder="Tipo di imbarcazione, esigenze particolari...">{{ old('guest_notes') }}</textarea>
                                                </div>

                                                <button type="submit" class="btn-primary w-full justify-center">
                                                    Invia richiesta di prenotazione
                                                </button>
                                            </div>
                                        </form>

                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                const startEl = document.getElementById('start_date');
                                                const endEl = document.getElementById('end_date');
                                                const previewEl = document.getElementById('price-preview');
                                                const daysEl = document.getElementById('preview-days');
                                                const priceEl = document.getElementById('preview-price');
                                                const pricePerDay = {{ $berth->price_per_day }};
                                                const pricePerWeek = {{ $berth->price_per_week ?? 0 }};
                                                const pricePerMonth = {{ $berth->price_per_month ?? 0 }};

                                                function updatePreview() {
                                                    if (!startEl.value || !endEl.value) {
                                                        previewEl.classList.add('hidden');
                                                        return;
                                                    }
                                                    const start = new Date(startEl.value);
                                                    const end = new Date(endEl.value);
                                                    const days = Math.round((end - start) / (1000 * 60 * 60 * 24));

                                                    if (days < 1) {
                                                        previewEl.classList.add('hidden');
                                                        return;
                                                    }

                                                    let total = pricePerDay * days;

                                                    if (pricePerWeek > 0 && days >= 7) {
                                                        const weeks = Math.floor(days / 7);
                                                        const rem = days % 7;
                                                        const weeklyTotal = (weeks * pricePerWeek) + (rem * pricePerDay);
                                                        total = Math.min(total, weeklyTotal);
                                                    }

                                                    if (pricePerMonth > 0 && days >= 30) {
                                                        const months = Math.floor(days / 30);
                                                        const rem = days % 30;
                                                        const monthlyTotal = (months * pricePerMonth) + (rem * pricePerDay);
                                                        total = Math.min(total, monthlyTotal);
                                                    }

                                                    daysEl.textContent = days;
                                                    priceEl.textContent = '\u20AC ' + total.toLocaleString('it-IT', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                                    previewEl.classList.remove('hidden');
                                                }

                                                startEl.addEventListener('change', function() {
                                                    if (startEl.value) {
                                                        const minEnd = new Date(startEl.value);
                                                        minEnd.setDate(minEnd.getDate() + 1);
                                                        endEl.min = minEnd.toISOString().split('T')[0];
                                                        if (endEl.value && endEl.value <= startEl.value) {
                                                            endEl.value = '';
                                                        }
                                                    }
                                                    updatePreview();
                                                });
                                                endEl.addEventListener('change', updatePreview);
                                            });
                                        </script>
                                    @else
                                        <p class="text-center text-sm text-slate-500">Questo e il tuo posto barca.</p>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="btn-primary w-full justify-center">
                                        Accedi per prenotare
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>

                    {{-- Proprietario --}}
                    <div class="card card-body">
                        <h3 class="text-lg font-bold text-slate-900 mb-4">Proprietario</h3>
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-full bg-gradient-to-br from-ocean-400 to-ocean-600 flex items-center justify-center">
                                <span class="text-white font-semibold text-sm">{{ strtoupper(substr($berth->owner->name, 0, 2)) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">{{ $berth->owner->name }}</p>
                                <p class="text-sm text-slate-500">Membro dal {{ $berth->owner->created_at->format('m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
