<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-slate-900 leading-tight">Gestione utenti</h2>
            <p class="text-sm text-slate-500 mt-1">Amministra gli utenti della piattaforma</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert-success mb-6">{{ session('success') }}</div>
            @endif

            {{-- Filtri --}}
            <div class="card card-body mb-6">
                <form method="GET" class="flex gap-4">
                    <x-text-input type="text" name="search" :value="request('search')" placeholder="Cerca nome o email..." class="flex-1" />
                    <select name="role" class="form-select">
                        <option value="">Tutti i ruoli</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="owner" {{ request('role') === 'owner' ? 'selected' : '' }}>Proprietario</option>
                        <option value="guest" {{ request('role') === 'guest' ? 'selected' : '' }}>Navigatore</option>
                    </select>
                    <x-primary-button>Filtra</x-primary-button>
                </form>
            </div>

            <div class="table-container">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead>
                        <tr>
                            <th class="table-header">Nome</th>
                            <th class="table-header">Email</th>
                            <th class="table-header">Ruolo</th>
                            <th class="table-header">Stato</th>
                            <th class="table-header">Registrato</th>
                            <th class="table-header">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($users as $user)
                        <tr class="table-row">
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-sm">
                                @switch($user->role->value)
                                    @case('admin')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 ring-1 ring-inset ring-red-500/20">{{ $user->role->label() }}</span>
                                        @break
                                    @case('owner')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-ocean-50 text-ocean-700 ring-1 ring-inset ring-ocean-500/20">{{ $user->role->label() }}</span>
                                        @break
                                    @case('guest')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-seafoam-50 text-seafoam-700 ring-1 ring-inset ring-seafoam-500/20">{{ $user->role->label() }}</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset {{ $user->is_active ? 'bg-seafoam-50 text-seafoam-700 ring-seafoam-500/20' : 'bg-red-50 text-red-700 ring-red-500/20' }}">
                                    {{ $user->is_active ? 'Attivo' : 'Disattivato' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                        @csrf @method('PUT')
                                        <button type="submit" class="text-sm font-medium transition-colors {{ $user->is_active ? 'text-red-600 hover:text-red-800' : 'text-seafoam-600 hover:text-seafoam-800' }}">
                                            {{ $user->is_active ? 'Disattiva' : 'Attiva' }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4">{{ $users->withQueryString()->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
