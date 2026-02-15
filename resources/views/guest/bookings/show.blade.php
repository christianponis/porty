<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('my-bookings') }}" class="text-slate-400 hover:text-ocean-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-bold text-xl text-slate-900 leading-tight">Prenotazione #{{ $booking->id }}</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert-success mb-6">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert-error mb-6">{{ session('error') }}</div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Dettaglio prenotazione --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="card card-body">
                        <div class="flex justify-between items-start mb-6">
                            <h3 class="text-lg font-bold text-slate-900">Dettagli prenotazione</h3>
                            <span class="badge-{{ $booking->status->value }}">{{ $booking->status->label() }}</span>
                        </div>

                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-ocean-50/50 rounded-xl p-4">
                                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Data arrivo</span>
                                    <span class="text-lg font-bold text-slate-900">{{ $booking->start_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="bg-ocean-50/50 rounded-xl p-4">
                                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Data partenza</span>
                                    <span class="text-lg font-bold text-slate-900">{{ $booking->end_date->format('d/m/Y') }}</span>
                                </div>
                            </div>

                            <div class="flex justify-between items-center py-3 border-t border-slate-100">
                                <span class="text-slate-600">Durata</span>
                                <span class="font-semibold text-slate-900">{{ $booking->total_days }} giorni</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-t border-slate-100">
                                <span class="text-slate-600">Totale</span>
                                <span class="text-2xl font-bold text-ocean-600">&euro; {{ number_format($booking->total_price, 2, ',', '.') }}</span>
                            </div>

                            @if($booking->guest_notes)
                                <div class="pt-3 border-t border-slate-100">
                                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Le tue note</span>
                                    <p class="text-slate-700">{{ $booking->guest_notes }}</p>
                                </div>
                            @endif

                            @if($booking->owner_notes)
                                <div class="pt-3 border-t border-slate-100">
                                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Note del proprietario</span>
                                    <p class="text-slate-700">{{ $booking->owner_notes }}</p>
                                </div>
                            @endif

                            @if($booking->cancelled_at)
                                <div class="pt-3 border-t border-slate-100">
                                    <span class="block text-sm text-red-500 font-medium">
                                        Cancellata {{ $booking->cancelled_by === 'guest' ? 'da te' : 'dal proprietario' }}
                                        il {{ $booking->cancelled_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        @if(in_array($booking->status->value, ['pending', 'confirmed']))
                            <div class="mt-6 pt-4 border-t border-slate-100">
                                <form method="POST" action="{{ route('my-bookings.cancel', $booking) }}">
                                    @csrf @method('PUT')
                                    <button type="submit" class="btn-danger"
                                        onclick="return confirm('Sei sicuro di voler cancellare questa prenotazione?')">
                                        Cancella prenotazione
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    {{-- Transazioni --}}
                    @if($booking->transactions->isNotEmpty())
                        <div class="card card-body">
                            <h3 class="text-lg font-bold text-slate-900 mb-4">Transazioni</h3>
                            <div class="table-container">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead>
                                        <tr>
                                            <th class="table-header">Tipo</th>
                                            <th class="table-header">Data</th>
                                            <th class="table-header text-right">Importo</th>
                                            <th class="table-header text-right">Stato</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($booking->transactions as $transaction)
                                            <tr class="table-row">
                                                <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $transaction->type->label() }}</td>
                                                <td class="px-4 py-3 text-sm text-slate-500">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                                <td class="px-4 py-3 text-sm font-semibold text-slate-900 text-right">&euro; {{ number_format($transaction->amount, 2, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-right">
                                                    <span class="badge-{{ $transaction->status->value }}">{{ $transaction->status->label() }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Sidebar: Info posto barca --}}
                <div class="space-y-6">
                    <div class="card card-body">
                        <h3 class="text-lg font-bold text-slate-900 mb-4">Posto barca</h3>
                        <div class="space-y-3">
                            <div>
                                <a href="{{ route('berths.show', $booking->berth) }}" class="text-ocean-600 hover:text-ocean-800 font-semibold transition-colors">
                                    {{ $booking->berth->title }}
                                </a>
                                <span class="text-xs text-slate-400 ml-1">{{ $booking->berth->code }}</span>
                            </div>
                            <div class="text-sm text-slate-600">
                                {{ $booking->berth->port->name }}<br>
                                {{ $booking->berth->port->city }}, {{ $booking->berth->port->province }}
                            </div>
                            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                                <div class="bg-ocean-50/50 rounded-xl p-2.5">
                                    <span class="block font-bold text-ocean-700">{{ $booking->berth->length_m }}m</span>
                                    <span class="text-slate-400">Lung.</span>
                                </div>
                                <div class="bg-ocean-50/50 rounded-xl p-2.5">
                                    <span class="block font-bold text-ocean-700">{{ $booking->berth->width_m }}m</span>
                                    <span class="text-slate-400">Larg.</span>
                                </div>
                                <div class="bg-ocean-50/50 rounded-xl p-2.5">
                                    <span class="block font-bold text-ocean-700">{{ $booking->berth->max_draft_m ?? '-' }}m</span>
                                    <span class="text-slate-400">Pesc.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-body">
                        <h3 class="text-lg font-bold text-slate-900 mb-4">Proprietario</h3>
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-full bg-gradient-to-br from-ocean-400 to-ocean-600 flex items-center justify-center">
                                <span class="text-white font-semibold text-sm">{{ strtoupper(substr($booking->berth->owner->name, 0, 2)) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">{{ $booking->berth->owner->name }}</p>
                                @if($booking->berth->owner->phone)
                                    <p class="text-sm text-slate-500">{{ $booking->berth->owner->phone }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
