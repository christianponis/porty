<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Porty - Marketplace posti barca</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50">
    {{-- Header --}}
    <header class="glass border-b border-slate-200/60 shadow-nav sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <a href="/"><img src="/porty_logo.png" alt="Porty" class="h-10"></a>
            <nav class="flex items-center gap-4">
                <a href="{{ route('search') }}" class="text-sm font-medium text-slate-600 hover:text-ocean-600 transition-colors">Cerca posti barca</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary text-sm">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-ocean-600 transition-colors">Accedi</a>
                    <a href="{{ route('register') }}" class="btn-primary text-sm">Registrati</a>
                @endauth
            </nav>
        </div>
    </header>

    {{-- Banner Demo --}}
    <div class="bg-gradient-to-r from-amber-500 via-orange-500 to-amber-500 border-b-2 border-amber-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex items-center justify-center gap-3 text-white">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm md:text-base font-semibold text-center">
                    <span class="font-bold">VERSIONE DEMO</span> - Questa è una versione di prova non funzionante al momento. I pagamenti e le prenotazioni non sono attivi.
                </p>
            </div>
        </div>
    </div>

    {{-- Hero con slideshow --}}
    <section class="relative overflow-hidden min-h-[520px] lg:min-h-[600px]" x-data="heroSlideshow()" x-init="start()">
        {{-- Slideshow background --}}
        <div class="absolute inset-0">
            @foreach($heroImages as $i => $img)
                <div class="absolute inset-0 transition-opacity duration-1000 ease-in-out"
                     :class="current === {{ $i }} ? 'opacity-100' : 'opacity-0'">
                    <img src="{{ $img }}"
                         alt="Porto" class="w-full h-full object-cover">
                </div>
            @endforeach
            {{-- Overlay leggero per leggibilita --}}
            <div class="absolute inset-0 bg-gradient-to-b from-slate-900/40 via-slate-900/20 to-slate-900/50"></div>
        </div>

        {{-- Contenuto --}}
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white tracking-tight mb-6" style="text-shadow: 0 2px 12px rgba(0,0,0,0.6), 0 1px 3px rgba(0,0,0,0.4)">
                    Trova il tuo <span class="text-ocean-200">posto barca</span>
                </h1>
                <p class="text-lg md:text-xl text-white/90 mb-10 max-w-2xl mx-auto leading-relaxed" style="text-shadow: 0 1px 8px rgba(0,0,0,0.5), 0 1px 2px rgba(0,0,0,0.3)">
                    Il marketplace peer-to-peer per scambiare posti barca nei porti italiani.
                    Semplice, sicuro, conveniente.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('search') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-ocean-700 font-bold rounded-xl hover:bg-ocean-50 hover:shadow-lg transition-all duration-300 text-base">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Cerca un posto barca
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white/15 text-white font-bold rounded-xl border border-white/30 hover:bg-white/25 hover:border-white/50 transition-all duration-300 backdrop-blur-sm text-base shadow-lg">
                        Pubblica il tuo posto
                    </a>
                </div>
            </div>

            {{-- Indicatori slide --}}
            <div class="flex justify-center gap-2 mt-10">
                @for($i = 0; $i < $heroImages->count(); $i++)
                    <button @click="goTo({{ $i }})"
                        class="w-2.5 h-2.5 rounded-full transition-all duration-300"
                        :class="current === {{ $i }} ? 'bg-white w-8' : 'bg-white/40 hover:bg-white/60'">
                    </button>
                @endfor
            </div>

        </div>

        {{-- Wave bottom --}}
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,40 C360,80 720,0 1080,50 C1260,70 1380,55 1440,40 L1440,80 L0,80 Z" fill="#f8fafc"/>
            </svg>
        </div>
    </section>

    <script>
        function heroSlideshow() {
            return {
                current: 0,
                total: {{ $heroImages->count() }},
                interval: null,
                start() {
                    this.interval = setInterval(() => this.next(), 5000);
                },
                next() {
                    this.current = (this.current + 1) % this.total;
                },
                goTo(index) {
                    this.current = index;
                    clearInterval(this.interval);
                    this.start();
                }
            }
        }
    </script>

    {{-- Come funziona --}}
    <section class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 tracking-tight">Come funziona</h2>
                <p class="mt-4 text-lg text-slate-500 max-w-xl mx-auto">Tre semplici passi per ormeggiare la tua barca</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
                @foreach([
                    ['num' => '1', 'title' => 'Cerca', 'desc' => 'Trova il posto barca ideale per la tua imbarcazione filtrando per zona, dimensioni e prezzo.', 'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
                    ['num' => '2', 'title' => 'Prenota', 'desc' => 'Seleziona le date, invia la richiesta e attendi la conferma del proprietario.', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['num' => '3', 'title' => 'Ormeggia', 'desc' => 'Pagamento sicuro tramite la piattaforma. Ormeggia la tua barca e goditi il mare.', 'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z']
                ] as $step)
                    <div class="card card-body text-center group hover:shadow-card-hover hover:-translate-y-1 transition-all duration-300">
                        <div class="w-16 h-16 rounded-2xl bg-ocean-50 flex items-center justify-center mx-auto mb-5 group-hover:bg-ocean-100 group-hover:scale-110 transition-all duration-300">
                            <svg class="w-7 h-7 text-ocean-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $step['icon'] }}"/>
                            </svg>
                        </div>
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-ocean-600 text-white text-xs font-bold mb-3 mx-auto">{{ $step['num'] }}</span>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">{{ $step['title'] }}</h3>
                        <p class="text-slate-500 leading-relaxed">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Piu gettonati --}}
    @if(isset($topBerths) && $topBerths->isNotEmpty())
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-slate-900 tracking-tight">I piu gettonati</h2>
                <p class="mt-3 text-slate-500">I posti barca con le migliori valutazioni</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($topBerths as $berth)
                    <a href="{{ route('berths.show', $berth) }}" class="card group hover:shadow-card-hover hover:-translate-y-1 transition-all duration-300 overflow-hidden relative block">
                        @if($berth->port->image_url)
                            <div class="overflow-hidden">
                                <img src="{{ $berth->port->image_url }}" alt="{{ $berth->port->name }}" class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                        @else
                            <div class="h-40 bg-gradient-to-br from-ocean-100 via-ocean-50 to-slate-100 flex items-center justify-center">
                                <svg class="w-10 h-10 text-ocean-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9l2-2m0 0l7-7 7 7M5 7v11a2 2 0 002 2h10a2 2 0 002-2V7"/></svg>
                            </div>
                        @endif
                        <div class="p-6 pt-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-base font-bold text-slate-900 group-hover:text-ocean-700 transition-colors">{{ $berth->title }}</h3>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-ocean-50 text-ocean-700 ring-1 ring-inset ring-ocean-500/20">{{ $berth->code }}</span>
                            </div>
                            <p class="text-sm text-slate-500 mb-3">{{ $berth->port->name }} - {{ $berth->port->city }}</p>
                            @if($berth->getEffectiveAnchorCount() > 0 || $berth->review_count > 0)
                                <div class="flex items-center gap-2 mb-3">
                                    @if($berth->getEffectiveAnchorCount() > 0)
                                        <x-anchor-rating :count="$berth->getEffectiveAnchorCount()" :level="$berth->getEffectiveRatingLevel()->value" size="sm" />
                                    @endif
                                    @if($berth->review_count > 0)
                                        <x-review-stars :rating="$berth->review_average" :count="$berth->review_count" size="sm" />
                                    @endif
                                </div>
                            @endif
                            <div class="flex justify-between items-center pt-3 border-t border-slate-100">
                                <span class="text-sm text-slate-500">{{ $berth->length_m }}m x {{ $berth->width_m }}m</span>
                                <div>
                                    <span class="text-lg font-bold text-ocean-600">&euro; {{ number_format($berth->price_per_day, 0, ',', '.') }}</span>
                                    <span class="text-xs text-slate-400">/giorno</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Ultimi inseriti --}}
    @if(isset($latestBerths) && $latestBerths->isNotEmpty())
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Ultimi inseriti</h2>
                <p class="mt-3 text-slate-500">I posti barca appena aggiunti alla piattaforma</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($latestBerths as $berth)
                    <a href="{{ route('berths.show', $berth) }}" class="card group hover:shadow-card-hover hover:-translate-y-1 transition-all duration-300 overflow-hidden relative block">
                        @if($berth->port->image_url)
                            <div class="overflow-hidden">
                                <img src="{{ $berth->port->image_url }}" alt="{{ $berth->port->name }}" class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                        @else
                            <div class="h-40 bg-gradient-to-br from-seafoam-100 via-seafoam-50 to-slate-100 flex items-center justify-center">
                                <svg class="w-10 h-10 text-seafoam-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9l2-2m0 0l7-7 7 7M5 7v11a2 2 0 002 2h10a2 2 0 002-2V7"/></svg>
                            </div>
                        @endif
                        <div class="p-6 pt-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-base font-bold text-slate-900 group-hover:text-ocean-700 transition-colors">{{ $berth->title }}</h3>
                                <span class="text-xs text-slate-400">{{ $berth->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-slate-500 mb-3">{{ $berth->port->name }} - {{ $berth->port->city }}</p>
                            <div class="flex justify-between items-center pt-3 border-t border-slate-100">
                                <span class="text-sm text-slate-500">{{ $berth->length_m }}m x {{ $berth->width_m }}m</span>
                                <div>
                                    <span class="text-lg font-bold text-ocean-600">&euro; {{ number_format($berth->price_per_day, 0, ',', '.') }}</span>
                                    <span class="text-xs text-slate-400">/giorno</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="text-center mt-8">
                <a href="{{ route('search') }}" class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Vedi tutti i posti barca
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- Stats --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8"
                 x-data="{ shown: false }" x-intersect.once="shown = true">
                @foreach([
                    ['value' => \App\Models\Port::active()->count(), 'label' => 'Porti in Italia', 'suffix' => '+'],
                    ['value' => \App\Models\Berth::active()->available()->count(), 'label' => 'Posti disponibili', 'suffix' => ''],
                    ['value' => \App\Models\User::where('role', 'owner')->count(), 'label' => 'Proprietari attivi', 'suffix' => '+']
                ] as $stat)
                    <div class="text-center"
                         x-show="shown"
                         x-transition:enter="transition ease-out duration-700"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         style="transition-delay: {{ $loop->index * 200 }}ms">
                        <p class="text-4xl md:text-5xl font-bold text-gradient-ocean tabular-nums">{{ $stat['value'] }}{{ $stat['suffix'] }}</p>
                        <p class="text-slate-500 mt-2 text-lg">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="ocean-gradient text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-3">
                    <img src="/porty_logo.png" alt="Porty" class="h-8 brightness-0 invert">
                    <span class="text-ocean-200 text-sm">Parte dell'ecosistema EasyPortAI</span>
                </div>
                <div class="flex gap-6">
                    <a href="{{ route('search') }}" class="text-ocean-200 hover:text-white text-sm transition-colors">Cerca posti barca</a>
                    <a href="{{ route('login') }}" class="text-ocean-200 hover:text-white text-sm transition-colors">Accedi</a>
                    <a href="{{ route('register') }}" class="text-ocean-200 hover:text-white text-sm transition-colors">Registrati</a>
                </div>
            </div>
            <div class="border-t border-white/10 mt-8 pt-6 text-center">
                <p class="text-ocean-300 text-xs">&copy; {{ date('Y') }} Porty. Tutti i diritti riservati.</p>
            </div>
        </div>
    </footer>
</body>
</html>
