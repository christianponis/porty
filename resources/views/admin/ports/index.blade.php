<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">Gestione porti</h2>
                <p class="text-sm text-slate-500 mt-1">Amministra i porti della piattaforma</p>
            </div>
            <a href="{{ route('admin.ports.create') }}" class="btn-primary inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Nuovo porto
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert-success mb-6">{{ session('success') }}</div>
            @endif

            <div class="table-container">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead>
                        <tr>
                            <th class="table-header">Nome</th>
                            <th class="table-header">Citta</th>
                            <th class="table-header">Regione</th>
                            <th class="table-header">Posti barca</th>
                            <th class="table-header">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($ports as $port)
                        <tr class="table-row">
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900">{{ $port->name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ $port->city }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ $port->region }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ $port->berths_count }}</td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('admin.ports.edit', $port) }}" class="text-ocean-600 hover:text-ocean-800 font-medium transition-colors">Modifica</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4">{{ $ports->withQueryString()->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
