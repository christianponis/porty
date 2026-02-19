<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('owner.berths.index') }}" class="text-slate-400 hover:text-ocean-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">{{ $berth->title }}</h2>
            </div>
            <a href="{{ route('owner.berths.edit', $berth) }}" class="btn-primary inline-flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                Modifica
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert-success mb-6">{{ session('success') }}</div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Info posto --}}
                <div class="card card-body lg:col-span-1">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Dettagli</h3>
                    <dl class="space-y-3">
                        <div><dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Porto</dt><dd class="text-sm text-slate-900 mt-0.5">{{ $berth->port->name }} - {{ $berth->port->city }}</dd></div>
                        <div><dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Codice</dt><dd class="text-sm font-mono text-slate-900 mt-0.5">{{ $berth->code }}</dd></div>
                        <div><dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Dimensioni</dt><dd class="text-sm text-slate-900 mt-0.5">{{ $berth->length_m }}m x {{ $berth->width_m }}m (pescaggio: {{ $berth->max_draft_m ?? '-' }}m)</dd></div>
                        <div><dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Prezzo/giorno</dt><dd class="text-sm font-bold text-ocean-600 mt-0.5">&euro; {{ number_format($berth->price_per_day, 2, ',', '.') }}</dd></div>
                        @if($berth->price_per_week)<div><dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Prezzo/settimana</dt><dd class="text-sm text-slate-900 mt-0.5">&euro; {{ number_format($berth->price_per_week, 2, ',', '.') }}</dd></div>@endif
                        @if($berth->price_per_month)<div><dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Prezzo/mese</dt><dd class="text-sm text-slate-900 mt-0.5">&euro; {{ number_format($berth->price_per_month, 2, ',', '.') }}</dd></div>@endif
                        <div>
                            <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Stato</dt>
                            <dd class="mt-0.5">
                                @switch($berth->status->value)
                                    @case('available')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-seafoam-50 text-seafoam-700 ring-1 ring-inset ring-seafoam-500/20">{{ $berth->status->label() }}</span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-500/20">{{ $berth->status->label() }}</span>
                                @endswitch
                            </dd>
                        </div>
                    </dl>

                    @if($berth->amenities)
                        <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mt-5 mb-2">Servizi</h4>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($berth->amenities as $amenity)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-seafoam-50 text-seafoam-700 ring-1 ring-inset ring-seafoam-500/20">{{ $amenity }}</span>
                            @endforeach
                        </div>
                    @endif

                    {{-- Rating --}}
                    <div class="mt-5 pt-4 border-t border-slate-100">
                        <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Rating</h4>
                        @if($berth->getEffectiveAnchorCount() > 0)
                            <div class="mb-2">
                                <x-anchor-rating :count="$berth->getEffectiveAnchorCount()" :level="$berth->getEffectiveRatingLevel()?->value ?? 'grey'" size="md" :showLabel="true" />
                            </div>
                        @else
                            <p class="text-sm text-slate-400 mb-2">Nessun rating ancora</p>
                        @endif
                        @if($berth->review_count > 0)
                            <div class="flex items-center gap-2">
                                <x-review-stars :rating="$berth->review_average" :count="$berth->review_count" size="sm" />
                            </div>
                        @endif
                        @if(!$berth->selfAssessment || $berth->selfAssessment->status->value !== 'submitted')
                            <a href="{{ route('owner.assessment.show', $berth) }}" class="mt-3 inline-flex items-center gap-1.5 text-sm text-ocean-600 hover:text-ocean-800 font-medium transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Compila autovalutazione
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Colonna destra --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Disponibilita --}}
                    <div class="card card-body">
                        <h3 class="text-lg font-bold text-slate-900 mb-4">Periodi di disponibilita</h3>

                        @if($berth->availabilities->isEmpty())
                            <p class="text-slate-500 text-sm mb-4">Nessun periodo impostato. Aggiungi almeno un periodo per ricevere prenotazioni.</p>
                        @else
                            <div class="space-y-2 mb-4">
                                @foreach($berth->availabilities->sortBy('start_date') as $availability)
                                    <div class="flex items-center justify-between rounded-xl p-3.5 {{ $availability->is_available ? 'bg-seafoam-50' : 'bg-red-50' }}">
                                        <div class="flex items-center gap-3">
                                            <div class="w-2.5 h-2.5 rounded-full {{ $availability->is_available ? 'bg-seafoam-500' : 'bg-red-500' }}"></div>
                                            <span class="text-sm font-medium text-slate-900">
                                                {{ $availability->start_date->format('d/m/Y') }} - {{ $availability->end_date->format('d/m/Y') }}
                                            </span>
                                            @if($availability->note)
                                                <span class="text-xs text-slate-500">- {{ $availability->note }}</span>
                                            @endif
                                        </div>
                                        <form method="POST" action="{{ route('owner.availability.destroy', [$berth, $availability]) }}">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium transition-colors" onclick="return confirm('Rimuovere questo periodo?')">Rimuovi</button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Form aggiungi periodo --}}
                        <form method="POST" action="{{ route('owner.availability.store', $berth) }}" class="bg-ocean-50/50 rounded-xl p-4">
                            @csrf
                            <h4 class="text-sm font-semibold text-ocean-800 mb-3">Aggiungi periodo disponibile</h4>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                <div>
                                    <x-input-label for="start_date" value="Dal" />
                                    <x-text-input type="date" id="start_date" name="start_date" class="mt-1 block w-full text-sm" required min="{{ now()->addDay()->format('Y-m-d') }}" />
                                    <x-input-error :messages="$errors->get('start_date')" class="mt-1" />
                                </div>
                                <div>
                                    <x-input-label for="end_date" value="Al" />
                                    <x-text-input type="date" id="end_date" name="end_date" class="mt-1 block w-full text-sm" required />
                                    <x-input-error :messages="$errors->get('end_date')" class="mt-1" />
                                </div>
                                <div>
                                    <x-input-label for="note" value="Nota (opz.)" />
                                    <x-text-input type="text" id="note" name="note" class="mt-1 block w-full text-sm" placeholder="es. stagione estiva" />
                                </div>
                                <div class="flex items-end">
                                    <x-primary-button class="w-full justify-center">Aggiungi</x-primary-button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Prenotazioni --}}
                    <div class="card card-body">
                        <h3 class="text-lg font-bold text-slate-900 mb-4">Prenotazioni</h3>
                        @if($berth->bookings->isEmpty())
                            <div class="text-center py-6">
                                <p class="text-slate-500">Nessuna prenotazione per questo posto.</p>
                            </div>
                        @else
                            <div class="table-container">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead>
                                        <tr>
                                            <th class="table-header">Ospite</th>
                                            <th class="table-header">Date</th>
                                            <th class="table-header">Importo</th>
                                            <th class="table-header">Stato</th>
                                            <th class="table-header">Azioni</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($berth->bookings->sortByDesc('created_at') as $booking)
                                        <tr class="table-row">
                                            <td class="px-4 py-3 text-sm">
                                                <span class="font-semibold text-slate-900">{{ $booking->guest->name }}</span>
                                                <span class="block text-xs text-slate-400">{{ $booking->guest->email }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-slate-600">{{ $booking->start_date->format('d/m/Y') }} - {{ $booking->end_date->format('d/m/Y') }}<br><span class="text-xs text-slate-400">{{ $booking->total_days }} gg</span></td>
                                            <td class="px-4 py-3 text-sm font-semibold text-slate-900">&euro; {{ number_format($booking->total_price, 2, ',', '.') }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                <span class="badge-{{ $booking->status->value }}">{{ $booking->status->label() }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                @if($booking->status->value === 'pending')
                                                    <form method="POST" action="{{ route('owner.bookings.update', $booking) }}" class="inline">
                                                        @csrf @method('PUT')
                                                        <input type="hidden" name="status" value="confirmed">
                                                        <button type="submit" class="text-seafoam-600 hover:text-seafoam-800 text-xs font-semibold transition-colors">Conferma</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('owner.bookings.update', $booking) }}" class="inline ml-2">
                                                        @csrf @method('PUT')
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-semibold transition-colors" onclick="return confirm('Rifiutare questa prenotazione?')">Rifiuta</button>
                                                    </form>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- Recensioni --}}
                    <div class="card card-body">
                        <h3 class="text-lg font-bold text-slate-900 mb-4">Recensioni</h3>
                        @if($berth->reviews->isEmpty())
                            <div class="text-center py-6">
                                <p class="text-slate-500">Nessuna recensione per questo posto.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($berth->reviews->sortByDesc('created_at')->take(10) as $review)
                                    <div class="border-b border-slate-100 last:border-0 pb-3 last:pb-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-semibold text-slate-900">{{ $review->guest->name }}</span>
                                                @if($review->is_verified)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-seafoam-50 text-seafoam-700">Verificata</span>
                                                @endif
                                            </div>
                                            <x-review-stars :rating="$review->average_rating" size="sm" />
                                        </div>
                                        @if($review->comment)
                                            <p class="text-sm text-slate-600 mt-1">{{ $review->comment }}</p>
                                        @endif
                                        <span class="text-xs text-slate-400">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
