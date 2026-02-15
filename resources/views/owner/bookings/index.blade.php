<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-slate-900 leading-tight">Prenotazioni ricevute</h2>
            <p class="text-sm text-slate-500 mt-1">Gestisci le prenotazioni dei tuoi posti barca</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert-success mb-6">{{ session('success') }}</div>
            @endif

            <div class="table-container">
                @if($bookings->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                        </div>
                        <p class="text-slate-500">Nessuna prenotazione ricevuta.</p>
                    </div>
                @else
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead>
                            <tr>
                                <th class="table-header">Ospite</th>
                                <th class="table-header">Posto</th>
                                <th class="table-header">Date</th>
                                <th class="table-header">Importo</th>
                                <th class="table-header">Stato</th>
                                <th class="table-header">Azioni</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($bookings as $booking)
                            <tr class="table-row">
                                <td class="px-6 py-4 text-sm">
                                    <span class="font-semibold text-slate-900">{{ $booking->guest->name }}</span>
                                    <span class="block text-xs text-slate-400">{{ $booking->guest->email }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $booking->berth->code }} - {{ $booking->berth->port->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $booking->start_date->format('d/m/Y') }} - {{ $booking->end_date->format('d/m/Y') }}<br><span class="text-xs text-slate-400">{{ $booking->total_days }} giorni</span></td>
                                <td class="px-6 py-4 text-sm font-semibold text-slate-900">&euro; {{ number_format($booking->total_price, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="badge-{{ $booking->status->value }}">{{ $booking->status->label() }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($booking->status->value === 'pending')
                                        <form method="POST" action="{{ route('owner.bookings.update', $booking) }}" class="inline">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="text-seafoam-600 hover:text-seafoam-800 text-xs font-semibold transition-colors">Conferma</button>
                                        </form>
                                        <form method="POST" action="{{ route('owner.bookings.update', $booking) }}" class="inline ml-2">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-semibold transition-colors">Rifiuta</button>
                                        </form>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="p-4">{{ $bookings->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
