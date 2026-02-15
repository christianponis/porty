<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Pagina non trovata | Porty</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-slate-50 flex items-center justify-center min-h-screen">
    <div class="text-center px-6 max-w-lg">
        <div class="w-24 h-24 rounded-3xl bg-ocean-50 flex items-center justify-center mx-auto mb-8 animate-float">
            <svg class="w-12 h-12 text-ocean-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>
            </svg>
        </div>
        <p class="text-7xl font-bold text-gradient-ocean mb-4">404</p>
        <h1 class="text-2xl font-bold text-slate-900 mb-3">Pagina non trovata</h1>
        <p class="text-slate-500 mb-8 leading-relaxed">La pagina che stai cercando non esiste o e stata spostata.</p>
        <div class="flex gap-3 justify-center">
            <a href="/" class="btn-primary">Torna alla home</a>
            <a href="/search" class="btn-secondary">Cerca posti barca</a>
        </div>
    </div>
</body>
</html>
