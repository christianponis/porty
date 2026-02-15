<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">I miei posti barca</h2>
                <p class="text-sm text-slate-500 mt-1">Gestisci i tuoi posti barca pubblicati</p>
            </div>
            <a href="{{ route('owner.berths.create') }}" class="btn-primary inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Nuovo posto
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert-success mb-6">{{ session('success') }}</div>
            @endif

            @if($berths->isEmpty())
                <div class="card card-body text-center py-12">
                    <div class="w-16 h-16 rounded-2xl bg-ocean-50 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-ocean-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    </div>
                    <p class="text-slate-500 mb-4">Non hai ancora nessun posto barca pubblicato.</p>
                    <a href="{{ route('owner.berths.create') }}" class="btn-primary inline-flex items-center gap-2">Pubblica il tuo primo posto</a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($berths as $berth)
                        <div class="card group hover:shadow-card-hover hover:-translate-y-1 transition-all duration-300 overflow-hidden relative">
                            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-ocean-500 to-ocean-600"></div>
                            <div class="p-6 pt-5">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-lg font-bold text-slate-900">{{ $berth->title }}</h3>
                                    @switch($berth->status->value)
                                        @case('available')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-seafoam-50 text-seafoam-700 ring-1 ring-inset ring-seafoam-500/20">{{ $berth->status->label() }}</span>
                                            @break
                                        @case('occupied')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 ring-1 ring-inset ring-red-500/20">{{ $berth->status->label() }}</span>
                                            @break
                                        @case('maintenance')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-500/20">{{ $berth->status->label() }}</span>
                                            @break
                                    @endswitch
                                </div>
                                <p class="text-sm text-slate-500">{{ $berth->port->name }} - Posto {{ $berth->code }}</p>
                                <p class="text-sm text-slate-400 mt-0.5">{{ $berth->length_m }}m x {{ $berth->width_m }}m</p>

                                <div class="mt-3 flex justify-between items-center">
                                    <span class="text-lg font-bold text-ocean-600">&euro; {{ number_format($berth->price_per_day, 0, ',', '.') }}/g</span>
                                    <span class="text-xs text-slate-400">{{ $berth->bookings_count }} prenotazioni</span>
                                </div>

                                <div class="mt-4 pt-4 border-t border-slate-100 flex gap-3">
                                    <a href="{{ route('owner.berths.edit', $berth) }}" class="text-sm text-ocean-600 hover:text-ocean-800 font-medium transition-colors">Modifica</a>
                                    <a href="{{ route('owner.berths.show', $berth) }}" class="text-sm text-slate-600 hover:text-slate-800 font-medium transition-colors">Dettagli</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-8">{{ $berths->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
