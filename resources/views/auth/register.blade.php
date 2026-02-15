<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nome completo" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Phone -->
        <div class="mt-4">
            <x-input-label for="phone" value="Telefono (opzionale)" />
            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Role -->
        <div class="mt-4" x-data="{ role: '{{ old('role', 'owner') }}' }">
            <x-input-label value="Registrati come" />
            <div class="mt-2 grid grid-cols-2 gap-3">
                <label @click="role = 'owner'" class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm transition-all" :class="role === 'owner' ? 'border-ocean-500 ring-2 ring-ocean-500' : 'border-gray-300'">
                    <input type="radio" name="role" value="owner" class="sr-only" :checked="role === 'owner'">
                    <span class="flex flex-1 flex-col">
                        <span class="block text-sm font-medium text-gray-900">Proprietario</span>
                        <span class="mt-1 text-xs text-gray-500">Ho un posto barca da affittare</span>
                    </span>
                </label>
                <label @click="role = 'guest'" class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm transition-all" :class="role === 'guest' ? 'border-ocean-500 ring-2 ring-ocean-500' : 'border-gray-300'">
                    <input type="radio" name="role" value="guest" class="sr-only" :checked="role === 'guest'">
                    <span class="flex flex-1 flex-col">
                        <span class="block text-sm font-medium text-gray-900">Navigatore</span>
                        <span class="mt-1 text-xs text-gray-500">Cerco un posto barca</span>
                    </span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Conferma password" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ocean-500" href="{{ route('login') }}">
                Hai gia un account?
            </a>

            <x-primary-button class="ms-4">
                Registrati
            </x-primary-button>
        </div>
    </form>

</x-guest-layout>
