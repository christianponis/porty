<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ports') }}" class="text-slate-400 hover:text-ocean-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-bold text-xl text-slate-900 leading-tight">Modifica porto: {{ $port->name }}</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="card card-body">
                <form method="POST" action="{{ route('admin.ports.update', $port) }}">
                    @csrf @method('PUT')
                    @include('admin.ports._form', ['port' => $port])

                    <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-slate-100">
                        <a href="{{ route('admin.ports') }}" class="btn-secondary">Annulla</a>
                        <x-primary-button>Salva modifiche</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
