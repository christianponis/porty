<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-slate-900">Gestione Rating</h2>
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
                            <th class="table-header text-center">Grigie</th>
                            <th class="table-header text-center">Blu</th>
                            <th class="table-header text-center">Dorate</th>
                            <th class="table-header text-center">Recensioni</th>
                            <th class="table-header text-center">Media</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($berths as $berth)
                            <tr class="table-row">
                                <td class="px-6 py-4">
                                    <span class="font-medium text-slate-900">{{ $berth->title }}</span>
                                    <span class="text-xs text-slate-400 ml-1">{{ $berth->code }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $berth->port?->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $berth->owner?->name }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if ($berth->grey_anchor_count)
                                        <x-anchor-rating :count="$berth->grey_anchor_count" level="grey" size="sm" />
                                    @else
                                        <span class="text-xs text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($berth->blue_anchor_count)
                                        <x-anchor-rating :count="$berth->blue_anchor_count" level="blue" size="sm" />
                                    @else
                                        <span class="text-xs text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($berth->gold_anchor_count)
                                        <x-anchor-rating :count="$berth->gold_anchor_count" level="gold" size="sm" />
                                    @else
                                        <span class="text-xs text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-slate-700">{{ $berth->review_count }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if ($berth->review_average)
                                        <x-review-stars :rating="$berth->review_average" size="sm" />
                                    @else
                                        <span class="text-xs text-slate-300">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-6 py-8 text-center text-slate-500">Nessun posto barca trovato.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $berths->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
