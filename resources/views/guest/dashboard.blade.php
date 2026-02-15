<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-slate-900 leading-tight">Dashboard Navigatore</h2>
            <p class="text-sm text-slate-500 mt-1">Le tue prenotazioni e i posti barca trovati</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert-success mb-6">{{ session('success') }}</div>
            @endif

            {{-- Stats --}}
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Panoramica</p>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <x-stat-card label="Prenotazioni totali" :value="$stats['total_bookings']" color="ocean"
                    :icon="'<svg class=\'w-5 h-5 text-ocean-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5\'/></svg>'" />
                <x-stat-card label="In attesa" :value="$stats['pending_bookings']" color="amber"
                    :icon="'<svg class=\'w-5 h-5 text-amber-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg>'" />
                <x-stat-card label="Confermate" :value="$stats['confirmed_bookings']" color="seafoam"
                    :icon="'<svg class=\'w-5 h-5 text-seafoam-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg>'" />
                <x-stat-card label="Spesa totale" :value="'&euro; ' . number_format($stats['total_spent'], 2, ',', '.')" color="ocean"
                    :icon="'<svg class=\'w-5 h-5 text-ocean-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z\'/></svg>'" />
            </div>

            {{-- Quick actions --}}
            <div class="flex flex-wrap gap-3 mb-8">
                <a href="{{ route('search') }}" class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Cerca un posto barca
                </a>
                <a href="{{ route('my-bookings') }}" class="btn-secondary inline-flex items-center gap-2">
                    Tutte le prenotazioni
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Prossime prenotazioni --}}
                <div class="lg:col-span-2 card">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-5">
                            <h3 class="text-lg font-bold text-slate-900">Prossime prenotazioni</h3>
                            <a href="{{ route('my-bookings') }}" class="text-sm text-ocean-600 hover:text-ocean-800 font-medium">Vedi tutte</a>
                        </div>
                        @if($upcoming_bookings->isEmpty())
                            <div class="text-center py-10">
                                <div class="w-16 h-16 rounded-2xl bg-ocean-50 flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-ocean-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <p class="text-slate-500 mb-3">Nessuna prenotazione in programma.</p>
                                <a href="{{ route('search') }}" class="btn-primary text-sm">Cerca un posto barca!</a>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($upcoming_bookings as $booking)
                                    <a href="{{ route('my-bookings.show', $booking) }}" class="block border border-slate-100 rounded-xl p-4 hover:bg-ocean-50/30 hover:border-ocean-200 hover:shadow-sm transition-all duration-200">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-semibold text-slate-900">{{ $booking->berth->title }}</p>
                                                <p class="text-sm text-slate-500">{{ $booking->berth->port->name }} - {{ $booking->berth->port->city }}</p>
                                                <p class="text-sm text-slate-400 mt-1">
                                                    {{ $booking->start_date->format('d/m/Y') }} - {{ $booking->end_date->format('d/m/Y') }}
                                                    ({{ $booking->total_days }} giorni)
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-bold text-lg text-ocean-600">&euro; {{ number_format($booking->total_price, 2, ',', '.') }}</p>
                                                <span class="badge-{{ $booking->status->value }}">{{ $booking->status->label() }}</span>
                                            </div>
                                        </div>
                                        @if($booking->status->value === 'confirmed')
                                            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center gap-2 text-xs text-slate-500">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                <span>{{ $booking->berth->owner->name }}</span>
                                                @if($booking->berth->owner->phone)
                                                    <span>&middot; {{ $booking->berth->owner->phone }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Prenotazioni passate --}}
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-900 mb-5">Recenti</h3>
                        @if($past_bookings->isEmpty())
                            <div class="text-center py-6">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="text-slate-500 text-sm">Nessuna prenotazione passata.</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach($past_bookings as $booking)
                                    <div class="p-3 rounded-xl border border-slate-100">
                                        <p class="text-sm font-semibold text-slate-900">{{ $booking->berth->title }}</p>
                                        <p class="text-xs text-slate-400 mt-0.5">{{ $booking->berth->port->name }}</p>
                                        <div class="flex justify-between items-center mt-2">
                                            <span class="text-xs text-slate-500">{{ $booking->start_date->format('d/m/Y') }}</span>
                                            <span class="badge-{{ $booking->status->value }}">{{ $booking->status->label() }}</span>
                                        </div>
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
