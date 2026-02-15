<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-slate-900 leading-tight">Transazioni</h2>
            <p class="text-sm text-slate-500 mt-1">Panoramica finanziaria della piattaforma</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Statistiche --}}
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Riepilogo</p>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
                <x-stat-card label="Pagamenti totali" :value="'&euro; ' . number_format($stats['total_payments'], 2, ',', '.')" color="ocean"
                    :icon="'<svg class=\'w-5 h-5 text-ocean-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z\'/></svg>'" />
                <x-stat-card label="Commissioni Porty" :value="'&euro; ' . number_format($stats['total_commissions'], 2, ',', '.')" color="seafoam"
                    :icon="'<svg class=\'w-5 h-5 text-seafoam-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z\'/></svg>'" />
                <x-stat-card label="Pagato agli Owner" :value="'&euro; ' . number_format($stats['total_payouts'], 2, ',', '.')" color="ocean"
                    :icon="'<svg class=\'w-5 h-5 text-ocean-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z\'/></svg>'" />
                <x-stat-card label="Rimborsi" :value="'&euro; ' . number_format($stats['total_refunds'], 2, ',', '.')" color="red" />
                <x-stat-card label="Payout in attesa" :value="'&euro; ' . number_format($stats['pending_payouts'], 2, ',', '.')" color="amber" />
            </div>

            {{-- Filtri --}}
            <div class="card card-body mb-6">
                <form method="GET" action="{{ route('admin.transactions') }}" class="flex gap-4 items-end">
                    <div>
                        <x-input-label value="Tipo" />
                        <select name="type" class="form-select mt-1 block text-sm">
                            <option value="">Tutti</option>
                            <option value="payment" {{ request('type') === 'payment' ? 'selected' : '' }}>Pagamento</option>
                            <option value="refund" {{ request('type') === 'refund' ? 'selected' : '' }}>Rimborso</option>
                            <option value="commission" {{ request('type') === 'commission' ? 'selected' : '' }}>Commissione</option>
                            <option value="payout" {{ request('type') === 'payout' ? 'selected' : '' }}>Payout</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Stato" />
                        <select name="status" class="form-select mt-1 block text-sm">
                            <option value="">Tutti</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completata</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>In attesa</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Fallita</option>
                        </select>
                    </div>
                    <x-primary-button>Filtra</x-primary-button>
                    @if(request()->hasAny(['type', 'status']))
                        <a href="{{ route('admin.transactions') }}" class="text-sm text-slate-500 hover:text-ocean-600 font-medium transition-colors">Reset</a>
                    @endif
                </form>
            </div>

            {{-- Tabella --}}
            <div class="table-container">
                @if($transactions->isEmpty())
                    <div class="p-12 text-center">
                        <p class="text-slate-500">Nessuna transazione.</p>
                    </div>
                @else
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead>
                            <tr>
                                <th class="table-header">ID</th>
                                <th class="table-header">Tipo</th>
                                <th class="table-header">Prenotazione</th>
                                <th class="table-header">Importo</th>
                                <th class="table-header">Commissione</th>
                                <th class="table-header">Owner</th>
                                <th class="table-header">Stato</th>
                                <th class="table-header">Data</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($transactions as $tx)
                            <tr class="table-row">
                                <td class="px-6 py-4 text-sm text-slate-400">#{{ $tx->id }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @switch($tx->type->value)
                                        @case('payment')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-ocean-50 text-ocean-700 ring-1 ring-inset ring-ocean-500/20">{{ $tx->type->label() }}</span>
                                            @break
                                        @case('refund')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 ring-1 ring-inset ring-red-500/20">{{ $tx->type->label() }}</span>
                                            @break
                                        @case('commission')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-seafoam-50 text-seafoam-700 ring-1 ring-inset ring-seafoam-500/20">{{ $tx->type->label() }}</span>
                                            @break
                                        @case('payout')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-500/20">{{ $tx->type->label() }}</span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($tx->booking)
                                        <span class="font-semibold text-slate-900">#{{ $tx->booking->id }}</span>
                                        <span class="block text-xs text-slate-400">
                                            {{ $tx->booking->berth->code ?? '' }} - {{ $tx->booking->guest->name ?? '' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-slate-900">&euro; {{ number_format($tx->amount, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm text-seafoam-600 font-medium">&euro; {{ number_format($tx->commission_amount, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">&euro; {{ number_format($tx->owner_amount, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="badge-{{ $tx->status->value }}">{{ $tx->status->label() }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="p-4">{{ $transactions->withQueryString()->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
