{{-- ============================================================
     MENÚ LATERAL: fijo en pantallas grandes y desplegable en celulares.
     Cada enlace aparece solo si el usuario tiene el permiso (@can).
     ============================================================ --}}

{{-- Fondo oscuro detrás del menú cuando se abre en el celular --}}
<div x-cloak x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
     class="fixed inset-0 z-40 bg-black/60 lg:hidden"></div>

<aside :class="{ '!translate-x-0': sidebarOpen }"
       class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-ink transition-transform duration-200 lg:translate-x-0">

    {{-- Marca: logo (ficha), nombre y eslogan --}}
    <div class="flex h-24 shrink-0 items-center gap-3 px-5">
        <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3">
            <x-brand-logo class="h-12 w-12 shrink-0" />
            <span class="min-w-0">
                <span class="block truncate font-display text-lg font-bold leading-tight text-gold">{{ $branding->trade_name }}</span>
                @if ($branding->slogan)
                    <span class="block truncate text-xs text-slate-400">{{ $branding->slogan }}</span>
                @endif
            </span>
        </a>
        <button type="button" @click="sidebarOpen = false" class="ms-auto rounded-md p-1 text-slate-400 hover:text-white lg:hidden" aria-label="Cerrar menú">
            <x-icon name="close" class="h-6 w-6" />
        </button>
    </div>
    <div class="mx-5 h-px bg-gradient-to-r from-transparent via-gold/60 to-transparent"></div>

    {{-- Enlaces agrupados por área del casino --}}
    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4" aria-label="Menú principal">
        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
            Inicio
        </x-nav-link>

        @canany(['ver-mesas', 'ver-juegos'])
            <p class="px-3 pb-1 pt-5 text-[11px] font-semibold uppercase tracking-[0.18em] text-gold/70">Sala de juegos</p>
        @endcanany
        @can('ver-mesas')
            <x-nav-link :href="route('mesas.index')" :active="request()->routeIs('mesas.*')" icon="table">
                Mesas de juego
            </x-nav-link>
        @endcan
        @can('ver-juegos')
            <x-nav-link :href="route('juegos.index')" :active="request()->routeIs('juegos.*')" icon="dice">
                Juegos
            </x-nav-link>
        @endcan

        @canany(['ver-clientes', 'ver-ventas', 'ver-fichas', 'ver-inventario'])
            <p class="px-3 pb-1 pt-5 text-[11px] font-semibold uppercase tracking-[0.18em] text-gold/70">Caja y clientes</p>
        @endcanany
        @can('ver-clientes')
            <x-nav-link :href="route('clientes.index')" :active="request()->routeIs('clientes.*')" icon="users">
                Clientes
            </x-nav-link>
        @endcan
        @can('ver-ventas')
            <x-nav-link :href="route('ventas.index')" :active="request()->routeIs('ventas.*')" icon="cart">
                Venta de fichas
            </x-nav-link>
        @endcan
        @can('ver-fichas')
            <x-nav-link :href="route('fichas.index')" :active="request()->routeIs('fichas.*')" icon="chip">
                Fichas en caja
            </x-nav-link>
        @endcan
        @can('ver-inventario')
            <x-nav-link :href="route('inventario.kardex')" :active="request()->routeIs('inventario.*')" icon="archive">
                Kardex de fichas
            </x-nav-link>
        @endcan

        @canany(['ver-proveedores', 'ver-compras'])
            <p class="px-3 pb-1 pt-5 text-[11px] font-semibold uppercase tracking-[0.18em] text-gold/70">Abastecimiento</p>
        @endcanany
        @can('ver-proveedores')
            <x-nav-link :href="route('proveedores.index')" :active="request()->routeIs('proveedores.*')" icon="building">
                Proveedores
            </x-nav-link>
        @endcan
        @can('ver-compras')
            <x-nav-link :href="route('compras.index')" :active="request()->routeIs('compras.*')" icon="truck">
                Compras de fichas
            </x-nav-link>
        @endcan

        @canany(['ver-auditoria', 'ver-branding'])
            <p class="px-3 pb-1 pt-5 text-[11px] font-semibold uppercase tracking-[0.18em] text-gold/70">Administración</p>
        @endcanany
        @can('ver-auditoria')
            <x-nav-link :href="route('auditoria.index')" :active="request()->routeIs('auditoria.*')" icon="shield">
                Auditoría
            </x-nav-link>
        @endcan
        @can('ver-branding')
            <x-nav-link :href="route('settings.branding.edit')" :active="request()->routeIs('settings.branding.*')" icon="swatch">
                Identidad del casino
            </x-nav-link>
        @endcan
        @include('partials.menu-extra')
</nav>

    {{-- Aviso de juego responsable (como en un casino real) --}}
    <div class="border-t border-white/10 px-5 py-4">
        <div class="flex items-center gap-3">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-gold/60 text-xs font-bold text-gold">18+</span>
            <p class="text-[11px] leading-snug text-slate-400">Juega con responsabilidad.<br>Prohibida la entrada a menores de edad.</p>
        </div>
        <p class="mt-3 truncate text-[10px] text-slate-600">© {{ date('Y') }} {{ $branding->legal_name }}</p>
    </div>
</aside>

{{-- ============================================================
     BARRA SUPERIOR: botón del menú (celular), título de la página, fecha y usuario.
     ============================================================ --}}
<div class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur lg:pl-72">
    <div class="flex min-h-[4rem] items-center gap-4 px-4 py-3 sm:px-6 lg:px-8">
        <button type="button" @click="sidebarOpen = true" class="-ms-1 rounded-md p-1 text-slate-500 hover:text-slate-800 lg:hidden" aria-label="Abrir menú">
            <x-icon name="menu" class="h-6 w-6" />
        </button>

        <div class="min-w-0 flex-1">
            {{ $header ?? '' }}
        </div>

        <span class="hidden text-sm capitalize text-slate-500 md:block">{{ now()->translatedFormat('l j \d\e F') }}</span>

        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="flex items-center gap-2 rounded-full border border-slate-200 bg-white py-1 pe-3 ps-1 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-ink text-sm font-bold text-gold ring-1 ring-gold/60">
                        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(Auth::user()->name, 0, 1)) }}
                    </span>
                    <span class="hidden text-left leading-tight sm:block">
                        <span class="block max-w-[10rem] truncate">{{ Auth::user()->name }}</span>
                        <span class="block text-[11px] uppercase tracking-wide text-gold-dark">{{ Auth::user()->getRoleNames()->first() ?? 'Sin rol' }}</span>
                    </span>
                    <x-icon name="chevron-down" class="h-4 w-4 text-slate-400" />
                </button>
            </x-slot>

            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">Mi perfil</x-dropdown-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        Cerrar sesión
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</div>