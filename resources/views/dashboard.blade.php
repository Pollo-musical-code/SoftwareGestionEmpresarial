<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-xl font-bold text-ink">Panel de control</h2>
    </x-slot>

    @php
        // Tonos de las tarjetas (las clases están completas para que Tailwind las genere)
        $tones = [
            'brand' => 'bg-brand/10 text-brand',
            'gold' => 'bg-amber-100 text-amber-700',
            'green' => 'bg-emerald-100 text-emerald-700',
            'blue' => 'bg-sky-100 text-sky-700',
            'red' => 'bg-rose-100 text-rose-700',
            'violet' => 'bg-violet-100 text-violet-700',
            'slate' => 'bg-slate-100 text-slate-700',
        ];
    @endphp

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Bienvenida: tapete verde con pintas --}}
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-felt to-felt-dark p-6 text-white shadow-lg ring-1 ring-gold/50 sm:p-8">
                <span class="pointer-events-none absolute -right-4 -top-8 select-none text-[11rem] leading-none text-white/10">♠</span>
                <span class="pointer-events-none absolute -bottom-12 right-32 hidden select-none text-[9rem] leading-none text-white/5 sm:block">♦</span>
                <div class="relative">
                    <p class="text-sm capitalize text-gold-light">{{ now()->translatedFormat('l j \d\e F \d\e Y') }}</p>
                    <h1 class="mt-1 font-display text-3xl font-bold sm:text-4xl">Bienvenido, {{ $user->name }}</h1>
                    <p class="mt-2 max-w-2xl text-sm text-emerald-100">
                        Aquí ves cómo va la sala de juegos, la caja y las fichas en inventario.
                        Cada tarjeta explica qué mide.
                    </p>
                    <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-black/30 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-gold ring-1 ring-gold/40">
                        Tu rol: {{ $roles->isNotEmpty() ? $roles->join(', ') : 'Sin rol asignado' }}
                    </span>
                </div>
            </div>

            {{-- Indicadores --}}
            @if ($stats->isNotEmpty())
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($stats as $stat)
                        <div class="flex items-start gap-4 rounded-xl border-t-4 border-gold bg-white p-5 shadow-sm">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full {{ $tones[$stat['tone'] ?? 'slate'] }}">
                                <x-icon :name="$stat['icon']" class="h-6 w-6" />
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-slate-500">{{ $stat['label'] }}</p>
                                <p class="truncate text-2xl font-bold text-slate-900">{{ $stat['value'] }}</p>
                                @if (! empty($stat['hint']))
                                    <p class="mt-0.5 text-xs text-slate-400">{{ $stat['hint'] }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Gráficos --}}
            @if ($charts->isNotEmpty())
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    @foreach ($charts as $chart)
                        <div class="rounded-xl bg-white p-5 shadow-sm {{ ! empty($chart['wide']) ? 'lg:col-span-2' : '' }}">
                            <h3 class="font-semibold text-slate-800">{{ $chart['title'] }}</h3>
                            <p class="mb-3 text-xs text-slate-400">{{ $chart['subtitle'] }}</p>
                            <div class="relative h-64">
                                <canvas id="chart-{{ $chart['id'] }}" data-chart="{{ json_encode($chart, JSON_UNESCAPED_UNICODE) }}" aria-label="{{ $chart['title'] }}"></canvas>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Fichas para reabastecer --}}
            @if ($fichasReabastecer->isNotEmpty())
                <div class="rounded-xl border-l-4 border-rose-600 bg-white p-5 shadow-sm">
                    <h3 class="font-semibold text-slate-800">Fichas para reabastecer</h3>
                    <p class="mb-3 text-xs text-slate-400">Quedan 5 unidades o menos. Conviene pedirlas al proveedor.</p>
                    <ul class="divide-y divide-slate-100">
                        @foreach ($fichasReabastecer as $ficha)
                            <li class="flex items-center gap-4 py-3">
                                <x-chip :valor="$ficha->valor" size="md" />
                                <div class="flex-1">
                                    <p class="font-medium text-slate-800">{{ $ficha->denominacion }}</p>
                                    <p class="text-sm text-rose-600">Quedan {{ $ficha->stock }} unidad(es)</p>
                                </div>
                                @can('crear-compras')
                                    <a href="{{ route('compras.create', ['ficha' => $ficha->id]) }}" class="text-sm font-semibold text-brand underline">Crear compra</a>
                                @endcan
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Accesos rápidos --}}
            <div>
                <h3 class="mb-3 font-semibold text-slate-800">Accesos rápidos</h3>
                @if ($modules->isEmpty())
                    <p class="rounded-lg bg-white p-4 text-sm text-slate-500 shadow-sm">No tienes módulos disponibles. Pide a un administrador que te asigne un rol.</p>
                @else
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($modules as $module)
                            <a href="{{ route($module['route']) }}" class="group flex items-start gap-4 rounded-xl bg-white p-5 shadow-sm ring-1 ring-transparent transition hover:ring-gold">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-ink text-gold">
                                    <x-icon :name="$module['icon']" class="h-6 w-6" />
                                </span>
                                <span>
                                    <span class="block font-semibold text-slate-800 group-hover:text-brand">{{ $module['title'] }}</span>
                                    <span class="block text-sm text-slate-500">{{ $module['description'] }}</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>