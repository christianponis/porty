<nav x-data="{ open: false }" class="glass border-b border-slate-200/60 shadow-nav sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="/" class="flex items-center gap-2.5 group">
                        <img src="/porty_logo.png" alt="Porty" class="h-10 transition-transform duration-300 group-hover:scale-105">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-8 sm:flex items-center">
                    <x-nav-link :href="route('search')" :active="request()->routeIs('search')">
                        <svg class="w-4 h-4 mr-1.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Cerca
                    </x-nav-link>

                    @auth
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('*.dashboard')">
                            Dashboard
                        </x-nav-link>

                        @if(auth()->user()->isAdmin())
                            {{-- Admin: dropdown per non affollare la nav --}}
                            <div class="relative" x-data="{ adminOpen: false }" @click.away="adminOpen = false">
                                <button @click="adminOpen = !adminOpen"
                                    class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.*') ? 'text-ocean-700 bg-ocean-50 font-semibold' : 'text-slate-600 hover:text-ocean-700 hover:bg-ocean-50/50' }}">
                                    Gestione
                                    <svg class="w-4 h-4 ml-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="adminOpen" x-cloak
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute left-0 mt-1 w-48 rounded-xl bg-white shadow-lg ring-1 ring-slate-900/5 py-1 z-50">
                                    <a href="{{ route('admin.users') }}" class="block px-4 py-2.5 text-sm hover:bg-ocean-50 hover:text-ocean-700 transition-colors {{ request()->routeIs('admin.users*') ? 'bg-ocean-50 text-ocean-700 font-semibold' : 'text-slate-700' }}">Utenti</a>
                                    <a href="{{ route('admin.ports') }}" class="block px-4 py-2.5 text-sm hover:bg-ocean-50 hover:text-ocean-700 transition-colors {{ request()->routeIs('admin.ports*') ? 'bg-ocean-50 text-ocean-700 font-semibold' : 'text-slate-700' }}">Porti</a>
                                    <a href="{{ route('admin.transactions') }}" class="block px-4 py-2.5 text-sm hover:bg-ocean-50 hover:text-ocean-700 transition-colors {{ request()->routeIs('admin.transactions*') ? 'bg-ocean-50 text-ocean-700 font-semibold' : 'text-slate-700' }}">Transazioni</a>
                                    <a href="{{ route('admin.ratings') }}" class="block px-4 py-2.5 text-sm hover:bg-ocean-50 hover:text-ocean-700 transition-colors {{ request()->routeIs('admin.ratings*') || request()->routeIs('admin.certifications*') ? 'bg-ocean-50 text-ocean-700 font-semibold' : 'text-slate-700' }}">Rating</a>
                                </div>
                            </div>
                        @endif

                        @if(auth()->user()->isOwner())
                            <x-nav-link :href="route('owner.berths.index')" :active="request()->routeIs('owner.berths*')">Posti barca</x-nav-link>
                            <x-nav-link :href="route('owner.bookings')" :active="request()->routeIs('owner.bookings*')">Prenotazioni</x-nav-link>
                            <x-nav-link :href="route('owner.nodi')" :active="request()->routeIs('owner.nodi')">
                                <svg class="w-5 h-5 mr-1 text-seafoam-600" fill="none" stroke="currentColor" stroke-width="16" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 400 400"><path d="M24 219.943C73.8113 211.642 120.446 207.312 169.006 155.742"/><path d="M199.206 130.337C250.916 99.842 326.254 151.462 318.438 211.641C310.211 274.983 221.728 286.903 176.199 281.931C130.669 276.958 109.033 266.778 88.6484 233.191"/><path d="M27.875 242.635C92.0763 233.78 144.795 214.431 190.039 169.025"/><path d="M221.711 148.426C250.633 136.713 322.466 192.991 283.323 225.705C235.939 265.308 146.315 263.113 119.195 227.691"/><path d="M79.9414 189.261C82.332 133.37 147.283 104.175 182.528 122.421C216.417 139.967 245.676 179.692 289.891 197.842"/><path d="M319.883 205.305C347.282 210.877 363.254 212.284 375.998 206.857"/><path d="M104.094 184.721C132.549 119.68 181.475 149.902 198.771 163.673C216.068 177.444 258.788 215.116 282.202 219.8"/><path d="M315.047 228.935C328.797 233.688 357.329 236.533 376 230.429"/></svg>
                                {{ number_format(auth()->user()->nodiWallet?->balance ?? 0, 0) }}
                            </x-nav-link>
                        @endif

                        @if(auth()->user()->isGuest())
                            <x-nav-link :href="route('my-bookings')" :active="request()->routeIs('my-bookings*')">Prenotazioni</x-nav-link>
                            <x-nav-link :href="route('guest.nodi')" :active="request()->routeIs('guest.nodi')">
                                <svg class="w-5 h-5 mr-1 text-seafoam-600" fill="none" stroke="currentColor" stroke-width="16" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 400 400"><path d="M24 219.943C73.8113 211.642 120.446 207.312 169.006 155.742"/><path d="M199.206 130.337C250.916 99.842 326.254 151.462 318.438 211.641C310.211 274.983 221.728 286.903 176.199 281.931C130.669 276.958 109.033 266.778 88.6484 233.191"/><path d="M27.875 242.635C92.0763 233.78 144.795 214.431 190.039 169.025"/><path d="M221.711 148.426C250.633 136.713 322.466 192.991 283.323 225.705C235.939 265.308 146.315 263.113 119.195 227.691"/><path d="M79.9414 189.261C82.332 133.37 147.283 104.175 182.528 122.421C216.417 139.967 245.676 179.692 289.891 197.842"/><path d="M319.883 205.305C347.282 210.877 363.254 212.284 375.998 206.857"/><path d="M104.094 184.721C132.549 119.68 181.475 149.902 198.771 163.673C216.068 177.444 258.788 215.116 282.202 219.8"/><path d="M315.047 228.935C328.797 233.688 357.329 236.533 376 230.429"/></svg>
                                {{ number_format(auth()->user()->nodiWallet?->balance ?? 0, 0) }}
                            </x-nav-link>
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
                            @if(auth()->user()->isOwner())
                                <x-dropdown-link :href="route('my-bookings')">
                                    <svg class="w-4 h-4 mr-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Le mie prenotazioni
                                </x-dropdown-link>
                            @endif
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
            <x-responsive-nav-link :href="route('search')" :active="request()->routeIs('search')">Cerca</x-responsive-nav-link>

            @auth
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('*.dashboard')">Dashboard</x-responsive-nav-link>

                @if(auth()->user()->isAdmin())
                    <div class="px-4 pt-3 pb-1"><span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Gestione</span></div>
                    <x-responsive-nav-link :href="route('admin.users')">Utenti</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.ports')">Porti</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.transactions')">Transazioni</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.ratings')">Rating</x-responsive-nav-link>
                @endif

                @if(auth()->user()->isOwner())
                    <div class="px-4 pt-3 pb-1"><span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Proprietario</span></div>
                    <x-responsive-nav-link :href="route('owner.berths.index')">Posti barca</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('owner.bookings')">Prenotazioni ricevute</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('owner.nodi')">
                        <svg class="w-5 h-5 mr-1 inline text-seafoam-600" fill="none" stroke="currentColor" stroke-width="16" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 400 400"><path d="M24 219.943C73.8113 211.642 120.446 207.312 169.006 155.742"/><path d="M199.206 130.337C250.916 99.842 326.254 151.462 318.438 211.641C310.211 274.983 221.728 286.903 176.199 281.931C130.669 276.958 109.033 266.778 88.6484 233.191"/><path d="M27.875 242.635C92.0763 233.78 144.795 214.431 190.039 169.025"/><path d="M221.711 148.426C250.633 136.713 322.466 192.991 283.323 225.705C235.939 265.308 146.315 263.113 119.195 227.691"/><path d="M79.9414 189.261C82.332 133.37 147.283 104.175 182.528 122.421C216.417 139.967 245.676 179.692 289.891 197.842"/><path d="M319.883 205.305C347.282 210.877 363.254 212.284 375.998 206.857"/><path d="M104.094 184.721C132.549 119.68 181.475 149.902 198.771 163.673C216.068 177.444 258.788 215.116 282.202 219.8"/><path d="M315.047 228.935C328.797 233.688 357.329 236.533 376 230.429"/></svg>
                        {{ number_format(auth()->user()->nodiWallet?->balance ?? 0, 0) }}
                    </x-responsive-nav-link>
                    <div class="px-4 pt-3 pb-1"><span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Ospite</span></div>
                    <x-responsive-nav-link :href="route('my-bookings')">Le mie prenotazioni</x-responsive-nav-link>
                @endif

                @if(auth()->user()->isGuest())
                    <x-responsive-nav-link :href="route('my-bookings')">Prenotazioni</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('guest.nodi')">
                        <svg class="w-5 h-5 mr-1 inline text-seafoam-600" fill="none" stroke="currentColor" stroke-width="16" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 400 400"><path d="M24 219.943C73.8113 211.642 120.446 207.312 169.006 155.742"/><path d="M199.206 130.337C250.916 99.842 326.254 151.462 318.438 211.641C310.211 274.983 221.728 286.903 176.199 281.931C130.669 276.958 109.033 266.778 88.6484 233.191"/><path d="M27.875 242.635C92.0763 233.78 144.795 214.431 190.039 169.025"/><path d="M221.711 148.426C250.633 136.713 322.466 192.991 283.323 225.705C235.939 265.308 146.315 263.113 119.195 227.691"/><path d="M79.9414 189.261C82.332 133.37 147.283 104.175 182.528 122.421C216.417 139.967 245.676 179.692 289.891 197.842"/><path d="M319.883 205.305C347.282 210.877 363.254 212.284 375.998 206.857"/><path d="M104.094 184.721C132.549 119.68 181.475 149.902 198.771 163.673C216.068 177.444 258.788 215.116 282.202 219.8"/><path d="M315.047 228.935C328.797 233.688 357.329 236.533 376 230.429"/></svg>
                        {{ number_format(auth()->user()->nodiWallet?->balance ?? 0, 0) }}
                    </x-responsive-nav-link>
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
