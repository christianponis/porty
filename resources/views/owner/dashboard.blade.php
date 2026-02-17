<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-slate-900 leading-tight">Dashboard Proprietario</h2>
            <p class="text-sm text-slate-500 mt-1">Gestisci i tuoi posti barca e le prenotazioni</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert-success mb-6">{{ session('success') }}</div>
            @endif

            {{-- Stats principali --}}
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Panoramica</p>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <x-stat-card label="Posti barca attivi" :value="$stats['active_berths'] . '/' . $stats['total_berths']" color="ocean"
                    :icon="'<svg class=\'w-5 h-5 text-ocean-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21\'/></svg>'" />
                <x-stat-card label="Prenotazioni in attesa" :value="$stats['pending_bookings']" color="amber"
                    :icon="'<svg class=\'w-5 h-5 text-amber-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg>'" />
                <x-stat-card label="Prenotazioni confermate" :value="$stats['confirmed_bookings']" color="seafoam"
                    :icon="'<svg class=\'w-5 h-5 text-seafoam-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg>'" />
                <x-stat-card label="Prenotazioni totali" :value="$stats['total_bookings']" color="ocean"
                    :icon="'<svg class=\'w-5 h-5 text-ocean-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5\'/></svg>'" />
            </div>

            {{-- Nodi --}}
            @if($stats['nodi_balance'] > 0)
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Nodi Wallet</p>
            <div class="grid grid-cols-1 md:grid-cols-1 gap-4 mb-6">
                <div class="card card-body flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-seafoam-50 flex items-center justify-center">
                        <svg class="w-10 h-10 text-seafoam-600" fill="none" stroke="currentColor" stroke-width="16" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 400 400"><path d="M24 219.943C73.8113 211.642 120.446 207.312 169.006 155.742"/><path d="M199.206 130.337C250.916 99.842 326.254 151.462 318.438 211.641C310.211 274.983 221.728 286.903 176.199 281.931C130.669 276.958 109.033 266.778 88.6484 233.191"/><path d="M27.875 242.635C92.0763 233.78 144.795 214.431 190.039 169.025"/><path d="M221.711 148.426C250.633 136.713 322.466 192.991 283.323 225.705C235.939 265.308 146.315 263.113 119.195 227.691"/><path d="M79.9414 189.261C82.332 133.37 147.283 104.175 182.528 122.421C216.417 139.967 245.676 179.692 289.891 197.842"/><path d="M319.883 205.305C347.282 210.877 363.254 212.284 375.998 206.857"/><path d="M104.094 184.721C132.549 119.68 181.475 149.902 198.771 163.673C216.068 177.444 258.788 215.116 282.202 219.8"/><path d="M315.047 228.935C328.797 233.688 357.329 236.533 376 230.429"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-slate-500">Saldo Nodi</p>
                        <p class="text-2xl font-bold text-seafoam-700">{{ number_format($stats['nodi_balance'], 0) }} Nodi</p>
                    </div>
                    <a href="{{ route('owner.nodi') }}" class="btn-ghost text-sm">Dettagli</a>
                </div>
            </div>
            @endif

            {{-- Stats finanziarie --}}
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Finanze</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <x-stat-card label="Volume prenotazioni" :value="'&euro; ' . number_format($stats['total_volume'], 2, ',', '.')" color="ocean"
                    :icon="'<svg class=\'w-5 h-5 text-ocean-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z\'/></svg>'" />
                <x-stat-card label="Guadagni ricevuti" :value="'&euro; ' . number_format($stats['total_earnings'], 2, ',', '.')" color="seafoam"
                    :icon="'<svg class=\'w-5 h-5 text-seafoam-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z\'/></svg>'" />
                <x-stat-card label="Payout in arrivo" :value="'&euro; ' . number_format($stats['pending_payouts'], 2, ',', '.')" color="amber"
                    :icon="'<svg class=\'w-5 h-5 text-amber-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg>'" />
            </div>

            {{-- Quick actions --}}
            <div class="flex flex-wrap gap-3 mb-8">
                <a href="{{ route('owner.berths.create') }}" class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Nuovo posto barca
                </a>
                <a href="{{ route('owner.bookings') }}" class="btn-secondary inline-flex items-center gap-2">
                    Tutte le prenotazioni
                </a>
                <a href="{{ route('owner.berths.index') }}" class="btn-secondary inline-flex items-center gap-2">
                    Gestisci posti barca
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Prenotazioni recenti --}}
                <div class="lg:col-span-2 card">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-5">
                            <h3 class="text-lg font-bold text-slate-900">Prenotazioni recenti</h3>
                            <a href="{{ route('owner.bookings') }}" class="text-sm text-ocean-600 hover:text-ocean-800 font-medium">Vedi tutte</a>
                        </div>
                        @if($recent_bookings->isEmpty())
                            <div class="text-center py-8">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                                </div>
                                <p class="text-slate-500">Nessuna prenotazione ricevuta.</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach($recent_bookings as $booking)
                                    <div class="flex justify-between items-center py-3 border-b border-slate-100 last:border-0">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">{{ $booking->guest->name }}</p>
                                            <p class="text-xs text-slate-400 mt-0.5">
                                                {{ $booking->berth->code }} &middot; {{ $booking->berth->port->name }}
                                                &middot; {{ $booking->start_date->format('d/m') }} - {{ $booking->end_date->format('d/m/Y') }}
                                                ({{ $booking->total_days }} gg)
                                            </p>
                                        </div>
                                        <div class="text-right flex items-center gap-3">
                                            <span class="text-sm font-semibold text-slate-900">&euro; {{ number_format($booking->total_price, 2, ',', '.') }}</span>
                                            <span class="badge-{{ $booking->status->value }}">{{ $booking->status->label() }}</span>
                                            @if($booking->status->value === 'pending')
                                                <form method="POST" action="{{ route('owner.bookings.update', $booking) }}" class="inline">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="status" value="confirmed">
                                                    <button type="submit" class="text-seafoam-600 hover:text-seafoam-800 text-xs font-semibold transition-colors">Conferma</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- I miei posti barca --}}
                <div class="card">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-5">
                            <h3 class="text-lg font-bold text-slate-900">I miei posti</h3>
                            <a href="{{ route('owner.berths.index') }}" class="text-sm text-ocean-600 hover:text-ocean-800 font-medium">Gestisci</a>
                        </div>
                        @if($berths->isEmpty())
                            <div class="text-center py-6">
                                <div class="w-12 h-12 rounded-xl bg-ocean-50 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-ocean-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                </div>
                                <p class="text-slate-500 text-sm mb-2">Nessun posto barca pubblicato.</p>
                                <a href="{{ route('owner.berths.create') }}" class="text-sm text-ocean-600 hover:text-ocean-800 font-medium">Pubblica il primo!</a>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach($berths as $berth)
                                    <a href="{{ route('owner.berths.show', $berth) }}" class="block p-3 rounded-xl hover:bg-ocean-50/30 transition-all duration-200 border border-slate-100 hover:border-ocean-200 hover:shadow-sm">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-sm font-semibold text-slate-900">{{ $berth->title }}</p>
                                                <p class="text-xs text-slate-400 mt-0.5">{{ $berth->port->name }} &middot; {{ $berth->code }}</p>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset {{ $berth->is_active ? 'bg-seafoam-50 text-seafoam-700 ring-seafoam-500/20' : 'bg-slate-50 text-slate-600 ring-slate-200' }}">
                                                {{ $berth->is_active ? 'Attivo' : 'Inattivo' }}
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap gap-3 mt-2 text-xs text-slate-500 items-center">
                                            <span>{{ $berth->bookings_count }} prenotazioni</span>
                                            <span>{{ $berth->availabilities_count }} periodi</span>
                                            @if($berth->reviews_count > 0)
                                                <span>{{ $berth->reviews_count }} recensioni</span>
                                            @endif
                                            @if($berth->getEffectiveAnchorCount() > 0)
                                                <x-anchor-rating :count="$berth->getEffectiveAnchorCount()" :level="$berth->getEffectiveRatingLevel()->value" size="sm" />
                                            @endif
                                            <span class="font-semibold text-ocean-600">&euro; {{ number_format($berth->price_per_day, 0, ',', '.') }}/gg</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
