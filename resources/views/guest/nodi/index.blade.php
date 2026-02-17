<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-slate-900">I miei Nodi</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        {{-- Wallet summary --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <x-stat-card label="Saldo attuale" :value="number_format($wallet->balance, 0) . ' Nodi'" color="ocean"
                :icon="'<svg class=\'w-5 h-5 text-ocean-600\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'16\' stroke-linecap=\'round\' stroke-linejoin=\'round\' viewBox=\'0 0 400 400\'><path d=\'M24 219.943C73.8113 211.642 120.446 207.312 169.006 155.742\'/><path d=\'M199.206 130.337C250.916 99.842 326.254 151.462 318.438 211.641C310.211 274.983 221.728 286.903 176.199 281.931C130.669 276.958 109.033 266.778 88.6484 233.191\'/><path d=\'M27.875 242.635C92.0763 233.78 144.795 214.431 190.039 169.025\'/><path d=\'M221.711 148.426C250.633 136.713 322.466 192.991 283.323 225.705C235.939 265.308 146.315 263.113 119.195 227.691\'/><path d=\'M79.9414 189.261C82.332 133.37 147.283 104.175 182.528 122.421C216.417 139.967 245.676 179.692 289.891 197.842\'/><path d=\'M319.883 205.305C347.282 210.877 363.254 212.284 375.998 206.857\'/><path d=\'M104.094 184.721C132.549 119.68 181.475 149.902 198.771 163.673C216.068 177.444 258.788 215.116 282.202 219.8\'/><path d=\'M315.047 228.935C328.797 233.688 357.329 236.533 376 230.429\'/></svg>'" />
            <x-stat-card label="Totale guadagnati" :value="number_format($wallet->total_earned, 0) . ' Nodi'" color="seafoam"
                :icon="'<svg class=\'w-5 h-5 text-seafoam-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 6v6m0 0v6m0-6h6m-6 0H6\'/></svg>'" />
            <x-stat-card label="Totale spesi" :value="number_format($wallet->total_spent, 0) . ' Nodi'" color="amber"
                :icon="'<svg class=\'w-5 h-5 text-amber-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M20 12H4\'/></svg>'" />
        </div>

        {{-- Transaction history --}}
        <div class="card">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-900">Storico transazioni</h3>
            </div>

            @if ($transactions->isEmpty())
                <div class="p-8 text-center text-slate-500">
                    <p>Nessuna transazione Nodi ancora.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="table-header">Data</th>
                                <th class="table-header">Tipo</th>
                                <th class="table-header">Descrizione</th>
                                <th class="table-header text-right">Importo</th>
                                <th class="table-header text-right">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $tx)
                                <tr class="table-row">
                                    <td class="px-6 py-4 text-sm text-slate-500">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="badge {{ $tx->amount >= 0 ? 'badge-seafoam' : 'badge-amber' }}">
                                            {{ $tx->type->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-700">{{ $tx->description ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-right {{ $tx->amount >= 0 ? 'text-seafoam-600' : 'text-red-500' }}">
                                        {{ $tx->amount >= 0 ? '+' : '' }}{{ number_format($tx->amount, 0) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500 text-right">{{ number_format($tx->balance_after, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
