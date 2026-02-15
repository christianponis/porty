<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Dashboard Admin</h2>
            <p class="text-sm text-slate-500 mt-1">Panoramica della piattaforma</p>
        </div>
    </x-slot>

    <div class="py-8 lg:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 alert-success">
                    <svg class="w-5 h-5 flex-shrink-0 text-seafoam-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Piattaforma --}}
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-3">Piattaforma</p>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <x-stat-card label="Utenti totali" :value="$stats['total_users']" />
                <x-stat-card label="Proprietari" :value="$stats['total_owners']" color="indigo" />
                <x-stat-card label="Navigatori" :value="$stats['total_guests']" color="seafoam" />
                <x-stat-card label="Porti attivi" :value="$stats['total_ports']" color="emerald" />
            </div>

            {{-- Operativo --}}
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-3">Operativo</p>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <x-stat-card label="Posti barca attivi" :value="$stats['active_berths'] . '/' . $stats['total_berths']" color="ocean" />
                <x-stat-card label="Prenotazioni totali" :value="$stats['total_bookings']" />
                <x-stat-card label="In attesa di conferma" :value="$stats['pending_bookings']" color="amber" />
                <x-stat-card label="Confermate" :value="$stats['confirmed_bookings']" color="seafoam" />
            </div>

            {{-- Finanziario --}}
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-3">Finanziario</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <x-stat-card label="Volume transato" :value="'&euro; ' . number_format($stats['total_volume'], 2, ',', '.')" color="ocean" />
                <x-stat-card label="Commissioni Porty" :value="'&euro; ' . number_format($stats['total_commissions'], 2, ',', '.')" color="seafoam" />
                <x-stat-card label="Payout in attesa" :value="'&euro; ' . number_format($stats['pending_payouts'], 2, ',', '.')" color="amber" />
            </div>

            {{-- Quick links --}}
            <div class="flex flex-wrap gap-3 mb-8">
                <a href="{{ route('admin.users') }}" class="btn-secondary">Gestisci utenti</a>
                <a href="{{ route('admin.ports') }}" class="btn-secondary">Gestisci porti</a>
                <a href="{{ route('admin.transactions') }}" class="btn-secondary">Tutte le transazioni</a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Prenotazioni recenti --}}
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Prenotazioni recenti</h3>
                        @if($recent_bookings->isEmpty())
                            <p class="text-slate-500">Nessuna prenotazione.</p>
                        @else
                            <div class="space-y-3">
                                @foreach($recent_bookings as $booking)
                                    <div class="flex justify-between items-center py-2 border-b border-slate-50 last:border-0">
                                        <div>
                                            <p class="text-sm font-medium text-slate-900">{{ $booking->guest->name }}</p>
                                            <p class="text-xs text-slate-400">{{ $booking->berth->port->name }} &middot; {{ $booking->start_date->format('d/m') }} - {{ $booking->end_date->format('d/m/Y') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-semibold text-slate-900">&euro; {{ number_format($booking->total_price, 2, ',', '.') }}</p>
                                            <span class="badge-{{ $booking->status->value }}">{{ $booking->status->label() }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Transazioni recenti --}}
                <div class="card">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-slate-900">Transazioni recenti</h3>
                            <a href="{{ route('admin.transactions') }}" class="text-sm text-ocean-600 hover:text-ocean-800 font-medium">Vedi tutte</a>
                        </div>
                        @if($recent_transactions->isEmpty())
                            <p class="text-slate-500">Nessuna transazione.</p>
                        @else
                            <div class="space-y-3">
                                @foreach($recent_transactions as $tx)
                                    <div class="flex justify-between items-center py-2 border-b border-slate-50 last:border-0">
                                        <div class="flex items-center gap-2">
                                            @switch($tx->type->value)
                                                @case('payment') <span class="badge-ocean">{{ $tx->type->label() }}</span> @break
                                                @case('refund') <span class="badge-red">{{ $tx->type->label() }}</span> @break
                                                @case('commission') <span class="badge-seafoam">{{ $tx->type->label() }}</span> @break
                                                @case('payout') <span class="badge-purple">{{ $tx->type->label() }}</span> @break
                                            @endswitch
                                            <span class="text-xs text-slate-400">{{ $tx->created_at->format('d/m H:i') }}</span>
                                        </div>
                                        <div class="text-right flex items-center gap-2">
                                            <span class="text-sm font-semibold text-slate-900">&euro; {{ number_format($tx->amount, 2, ',', '.') }}</span>
                                            @switch($tx->status->value)
                                                @case('completed') <span class="badge-confirmed">{{ $tx->status->label() }}</span> @break
                                                @case('pending') <span class="badge-pending">{{ $tx->status->label() }}</span> @break
                                                @case('failed') <span class="badge-cancelled">{{ $tx->status->label() }}</span> @break
                                            @endswitch
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
