<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Accesso negato | Porty</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-slate-50 flex items-center justify-center min-h-screen">
    <div class="text-center px-6 max-w-lg">
        <div class="w-24 h-24 rounded-3xl bg-red-50 flex items-center justify-center mx-auto mb-8 animate-float">
            <svg class="w-12 h-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
            </svg>
        </div>
        <p class="text-7xl font-bold bg-gradient-to-r from-red-500 to-red-400 bg-clip-text text-transparent mb-4">403</p>
        <h1 class="text-2xl font-bold text-slate-900 mb-3">Accesso negato</h1>
        <p class="text-slate-500 mb-8 leading-relaxed">{{ $exception->getMessage() ?: 'Non hai i permessi per accedere a questa pagina.' }}</p>
        <a href="/" class="btn-primary">Torna alla home</a>
    </div>
</body>
</html>
