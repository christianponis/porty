<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-slate-900 leading-tight">Le mie prenotazioni</h2>
            <p class="text-sm text-slate-500 mt-1">Tutte le tue prenotazioni di posti barca</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert-success mb-6">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert-error mb-6">{{ session('error') }}</div>
            @endif

            @if($bookings->isEmpty())
                <div class="card card-body text-center py-12">
                    <div class="w-16 h-16 rounded-2xl bg-ocean-50 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-ocean-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                    </div>
                    <p class="text-slate-500 mb-4">Non hai ancora prenotazioni.</p>
                    <a href="{{ route('search') }}" class="btn-primary inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Cerca un posto barca
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($bookings as $booking)
                        <div class="card group hover:shadow-card-hover transition-all duration-300 overflow-hidden relative">
                            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r
                                {{ $booking->status->value === 'pending' ? 'from-amber-400 to-amber-500' : '' }}
                                {{ $booking->status->value === 'confirmed' ? 'from-seafoam-400 to-seafoam-500' : '' }}
                                {{ $booking->status->value === 'cancelled' ? 'from-red-400 to-red-500' : '' }}
                                {{ $booking->status->value === 'completed' ? 'from-ocean-400 to-ocean-500' : '' }}
                            "></div>
                            <div class="p-6 pt-5">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-900">{{ $booking->berth->title }}</h3>
                                        <p class="text-sm text-slate-500">{{ $booking->berth->port->name }} - {{ $booking->berth->port->city }}</p>
                                        <p class="text-sm text-slate-400 mt-0.5">Proprietario: {{ $booking->berth->owner->name }}</p>
                                        <p class="text-sm text-slate-600 mt-2">
                                            {{ $booking->start_date->format('d/m/Y') }} - {{ $booking->end_date->format('d/m/Y') }}
                                            <span class="text-slate-400">({{ $booking->total_days }} giorni)</span>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xl font-bold text-ocean-600">&euro; {{ number_format($booking->total_price, 2, ',', '.') }}</p>
                                        <span class="badge-{{ $booking->status->value }} mt-1">{{ $booking->status->label() }}</span>

                                        @if(in_array($booking->status->value, ['pending', 'confirmed']))
                                            <form method="POST" action="{{ route('my-bookings.cancel', $booking) }}" class="mt-3">
                                                @csrf @method('PUT')
                                                <button type="submit" class="btn-danger text-xs" onclick="return confirm('Sei sicuro di voler cancellare questa prenotazione?')">
                                                    Cancella
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-8">{{ $bookings->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
