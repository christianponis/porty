<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-slate-900">Elimina account</h2>
        <p class="mt-1 text-sm text-slate-500">Una volta eliminato il tuo account, tutte le risorse e i dati saranno cancellati permanentemente. Prima di procedere, assicurati di aver scaricato tutti i dati che desideri conservare.</p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >Elimina account</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-slate-900">Sei sicuro di voler eliminare il tuo account?</h2>

            <p class="mt-1 text-sm text-slate-500">Una volta eliminato, tutte le risorse e i dati saranno cancellati permanentemente. Inserisci la tua password per confermare.</p>

            <div class="mt-6">
                <x-input-label for="password" value="Password" class="sr-only" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-3/4" placeholder="Password" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Annulla</x-secondary-button>
                <x-danger-button>Elimina account</x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
