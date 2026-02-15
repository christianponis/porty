<nav x-data="{ open: false }" class="glass border-b border-slate-200/60 shadow-nav sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="/" class="flex items-center gap-2.5 group">
                        <img src="/porty_logo.png" alt="Porty" class="h-24 transition-transform duration-300 group-hover:scale-105">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-8 sm:flex">
                    <x-nav-link :href="route('search')" :active="request()->routeIs('search')">
                        <svg class="w-4 h-4 mr-1.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Cerca posti barca
                    </x-nav-link>

                    @auth
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('*.dashboard')">
                            <svg class="w-4 h-4 mr-1.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg>
                            Dashboard
                        </x-nav-link>

                        @if(auth()->user()->isAdmin())
                            <x-nav-link :href="route('admin.users')" :active="request()->routeIs('admin.users*')">Utenti</x-nav-link>
                            <x-nav-link :href="route('admin.ports')" :active="request()->routeIs('admin.ports*')">Porti</x-nav-link>
                            <x-nav-link :href="route('admin.transactions')" :active="request()->routeIs('admin.transactions*')">Transazioni</x-nav-link>
                        @endif

                        @if(auth()->user()->isOwner())
                            <x-nav-link :href="route('owner.berths.index')" :active="request()->routeIs('owner.berths*')">I miei posti</x-nav-link>
                            <x-nav-link :href="route('owner.bookings')" :active="request()->routeIs('owner.bookings*')">Prenotazioni ricevute</x-nav-link>
                        @endif

                        @if(auth()->user()->isGuest() || auth()->user()->isOwner())
                            <x-nav-link :href="route('my-bookings')" :active="request()->routeIs('my-bookings*')">Le mie prenotazioni</x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Right side -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 sm:gap-3">
                @auth
                    <span class="badge
                        {{ auth()->user()->isAdmin() ? 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-500/20' : '' }}
                        {{ auth()->user()->isOwner() ? 'bg-ocean-50 text-ocean-700 ring-1 ring-inset ring-ocean-500/20' : '' }}
                        {{ auth()->user()->isGuest() ? 'bg-seafoam-50 text-seafoam-700 ring-1 ring-inset ring-seafoam-500/20' : '' }}
                    ">
                        {{ auth()->user()->role->label() }}
                    </span>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-all duration-200">
                                <span class="w-8 h-8 rounded-full bg-ocean-100 flex items-center justify-center text-ocean-700 text-xs font-bold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </span>
                                <span class="hidden md:inline">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                <svg class="w-4 h-4 mr-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Profilo
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    <svg class="w-4 h-4 mr-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Esci
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-ocean-600 transition-colors">Accedi</a>
                    <a href="{{ route('register') }}" class="btn-primary text-sm">Registrati</a>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-400 hover:text-ocean-600 hover:bg-ocean-50 focus:outline-none transition-all duration-200">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-100">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('search')" :active="request()->routeIs('search')">Cerca posti barca</x-responsive-nav-link>

            @auth
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('*.dashboard')">Dashboard</x-responsive-nav-link>

                @if(auth()->user()->isAdmin())
                    <x-responsive-nav-link :href="route('admin.users')">Utenti</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.ports')">Porti</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.transactions')">Transazioni</x-responsive-nav-link>
                @endif

                @if(auth()->user()->isOwner())
                    <x-responsive-nav-link :href="route('owner.berths.index')">I miei posti</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('owner.bookings')">Prenotazioni ricevute</x-responsive-nav-link>
                @endif

                @if(auth()->user()->isGuest() || auth()->user()->isOwner())
                    <x-responsive-nav-link :href="route('my-bookings')">Le mie prenotazioni</x-responsive-nav-link>
                @endif
            @endauth
        </div>

        @auth
            <div class="pt-4 pb-1 border-t border-slate-200">
                <div class="px-4 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-full bg-ocean-100 flex items-center justify-center text-ocean-700 text-sm font-bold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </span>
                    <div>
                        <div class="font-medium text-base text-slate-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-slate-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">Profilo</x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Esci</x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 pb-1 border-t border-slate-200">
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('login')">Accedi</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')">Registrati</x-responsive-nav-link>
                </div>
            </div>
        @endauth
    </div>
</nav>
