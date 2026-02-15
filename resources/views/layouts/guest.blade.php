<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Porty') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen flex">
            <!-- Decorative side panel (hidden on mobile) -->
            <div class="hidden lg:flex lg:w-1/2 ocean-gradient relative overflow-hidden">
                <div class="absolute inset-0 bg-grid-pattern opacity-20"></div>
                <div class="relative z-10 flex flex-col justify-center items-center px-12 text-white">
                    <img src="/porty_logo.png" alt="Porty" class="h-16 brightness-0 invert mb-8">
                    <h1 class="text-3xl font-bold mb-3 text-center">Marketplace posti barca</h1>
                    <p class="text-ocean-200 text-center max-w-sm">
                        Trova o condividi posti barca nei porti italiani.
                        Semplice, sicuro, conveniente.
                    </p>
                </div>
            </div>

            <!-- Form area -->
            <div class="flex-1 flex flex-col justify-center items-center px-4 py-8 sm:px-6 bg-slate-50">
                <div class="lg:hidden mb-8">
                    <a href="/">
                        <img src="/porty_logo.png" alt="Porty" class="h-12 mx-auto">
                    </a>
                    <p class="text-sm text-slate-400 text-center mt-2">Marketplace posti barca</p>
                </div>

                <div class="w-full sm:max-w-md">
                    <div class="card card-body">
                        {{ $slot }}
                    </div>
                    <p class="text-center text-xs text-slate-400 mt-6">
                        &copy; {{ date('Y') }} Porty &middot; Parte dell'ecosistema EasyPortAI
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
