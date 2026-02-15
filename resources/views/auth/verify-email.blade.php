<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Grazie per la registrazione! Prima di iniziare, verifica il tuo indirizzo email cliccando sul link che ti abbiamo appena inviato. Se non hai ricevuto l'email, te ne invieremo un'altra.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            Un nuovo link di verifica e stato inviato all'indirizzo email fornito durante la registrazione.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    Reinvia email di verifica
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ocean-500">
                Esci
            </button>
        </form>
    </div>
</x-guest-layout>
