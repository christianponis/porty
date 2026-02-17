<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-slate-900">Certificazioni Porty</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="card">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="table-header">Posto barca</th>
                            <th class="table-header">Porto</th>
                            <th class="table-header">Proprietario</th>
                            <th class="table-header">Stato</th>
                            <th class="table-header text-center">Ancore</th>
                            <th class="table-header">Scadenza</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($certifications as $cert)
                            <tr class="table-row">
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $cert->berth?->title }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $cert->berth?->port?->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $cert->berth?->owner?->name }}</td>
                                <td class="px-6 py-4">
                                    <span class="badge {{ match($cert->status->value) {
                                        'completed' => 'badge-seafoam',
                                        'scheduled' => 'badge-ocean',
                                        'expired' => 'badge-amber',
                                        default => 'badge-pending',
                                    } }}">{{ $cert->status->label() }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($cert->anchor_count)
                                        <x-anchor-rating :count="$cert->anchor_count" level="gold" size="sm" />
                                    @else
                                        <span class="text-xs text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">
                                    {{ $cert->valid_until?->format('d/m/Y') ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-8 text-center text-slate-500">Nessuna certificazione trovata.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $certifications->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
