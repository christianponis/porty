<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-slate-900">Lascia una recensione</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        {{-- Riepilogo prenotazione --}}
        <div class="card card-body mb-8">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-ocean-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-ocean-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900">{{ $booking->berth->title }}</h3>
                    <p class="text-sm text-slate-500">{{ $booking->berth->port->name }} - {{ $booking->berth->port->city }}</p>
                    <p class="text-xs text-slate-400">{{ $booking->start_date->format('d/m/Y') }} - {{ $booking->end_date->format('d/m/Y') }} ({{ $booking->total_days }} giorni)</p>
                </div>
            </div>
        </div>

        {{-- Form recensione --}}
        <form action="{{ route('my-bookings.review.store', $booking) }}" method="POST">
            @csrf

            <div class="card card-body mb-6">
                <h3 class="text-lg font-bold text-slate-900 mb-6">Valutazione</h3>

                @foreach ([
                    'rating_ormeggio' => 'Ormeggio',
                    'rating_servizi' => 'Servizi',
                    'rating_posizione' => 'Posizione',
                    'rating_qualita_prezzo' => 'Rapporto qualita/prezzo',
                    'rating_accoglienza' => 'Accoglienza',
                ] as $field => $label)
                    <div class="mb-5" x-data="{ rating: {{ old($field, 0) }} }">
                        <label class="block text-sm font-medium text-slate-700 mb-2">{{ $label }}</label>
                        <input type="hidden" name="{{ $field }}" :value="rating">
                        <div class="flex gap-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" @click="rating = {{ $i }}" class="focus:outline-none">
                                    <svg class="w-8 h-8 transition-colors" :class="rating >= {{ $i }} ? 'text-amber-400' : 'text-slate-200'" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endfor
                        </div>
                        @error($field) <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                @endforeach
            </div>

            <div class="card card-body mb-6">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Commento</h3>
                <textarea name="comment" rows="4" class="form-textarea w-full" placeholder="Racconta la tua esperienza...">{{ old('comment') }}</textarea>
                @error('comment') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Questionario verifica --}}
            @if ($booking->berth->selfAssessment)
                <div class="card card-body mb-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Verifica struttura</h3>
                    <p class="text-sm text-slate-500 mb-4">Aiutaci a verificare le condizioni del posto barca.</p>

                    @foreach ([
                        'has_electricity' => 'Erano presenti colonnine elettriche funzionanti?',
                        'has_water' => 'Era presente un allaccio acqua?',
                        'has_wifi' => 'Era disponibile il Wi-Fi?',
                        'has_security' => 'Era presente un servizio di sicurezza/guardiania?',
                        'has_showers' => 'Erano disponibili servizi igienici e docce?',
                    ] as $key => $question)
                        <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0" x-data="{ answer: null }">
                            <span class="text-sm text-slate-700">{{ $question }}</span>
                            <div class="flex gap-3">
                                <input type="hidden" name="verifications[{{ $loop->index }}][question_key]" value="{{ $key }}">
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="verifications[{{ $loop->index }}][answer]" value="1" x-model="answer" class="text-seafoam-600 focus:ring-seafoam-500">
                                    <span class="text-sm">Si</span>
                                </label>
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="verifications[{{ $loop->index }}][answer]" value="0" x-model="answer" class="text-red-500 focus:ring-red-400">
                                    <span class="text-sm">No</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="flex justify-between items-center">
                <a href="{{ route('my-bookings.show', $booking) }}" class="btn-secondary">Annulla</a>
                <button type="submit" class="btn-primary">Pubblica recensione</button>
            </div>
        </form>
    </div>
</x-app-layout>
