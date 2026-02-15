<section>
    <header>
        <h2 class="text-lg font-bold text-slate-900">Informazioni profilo</h2>
        <p class="mt-1 text-sm text-slate-500">Aggiorna il tuo nome, email e telefono.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nome" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="phone" value="Telefono" />
            <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" :value="old('phone', $user->phone)" autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div class="bg-slate-50 rounded-xl p-3.5">
            <span class="text-sm text-slate-500">Ruolo: </span>
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
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Salva</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-seafoam-600 font-medium">Salvato.</p>
            @endif
        </div>
    </form>
</section>
