<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $branding->trade_name }}</title>
        @if ($branding->logoUrl())
            <link rel="icon" href="{{ $branding->logoUrl() }}">
        @endif

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|playfair-display:600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <x-brand-styles />
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen bg-ink lg:grid lg:grid-cols-2">

            {{-- Panel de marca (solo en pantallas grandes) --}}
            <div class="relative hidden flex-col justify-between overflow-hidden bg-gradient-to-br from-ink via-ink-soft to-brand-950 p-12 text-white lg:flex">
                <span class="pointer-events-none absolute -right-6 -top-10 select-none text-[16rem] leading-none text-white/5">♠</span>
                <span class="pointer-events-none absolute -bottom-16 -left-6 select-none text-[16rem] leading-none text-brand/20">♥</span>

                <div class="relative flex items-center gap-3">
                    <x-brand-logo class="h-14 w-14" />
                    <span class="font-display text-2xl font-bold text-gold">{{ $branding->trade_name }}</span>
                </div>

                <div class="relative">
                    <p class="font-display text-5xl font-bold leading-tight">Bienvenido a la<br><span class="text-gold">sala de control</span></p>
                    <p class="mt-4 max-w-md text-slate-300">Mesas, fichas, clientes y caja en un solo lugar. Ingresa con tu usuario para comenzar.</p>
                    <p class="mt-6 text-3xl tracking-[0.5em] text-gold/80">♠ ♥ ♦ ♣</p>
                </div>

                <div class="relative flex items-center gap-3 text-sm text-slate-400">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full border border-gold/60 text-xs font-bold text-gold">18+</span>
                    Juega con responsabilidad. Prohibida la entrada a menores de edad.
                </div>
            </div>

            {{-- Formulario --}}
            <div class="flex min-h-screen items-center justify-center bg-stone-50 px-6 py-10 lg:min-h-0">
                <div class="w-full max-w-md">
                    {{-- Marca en pantallas pequeñas --}}
                    <div class="mb-6 flex items-center justify-center gap-3 lg:hidden">
                        <x-brand-logo class="h-12 w-12" />
                        <span class="font-display text-2xl font-bold text-ink">{{ $branding->trade_name }}</span>
                    </div>

                    <div class="rounded-2xl border-t-4 border-gold bg-white p-8 shadow-xl ring-1 ring-slate-200">
                        {{ $slot }}
                    </div>

                    <p class="mt-6 text-center text-xs text-slate-500">
                        {{ $branding->legal_name }} · NIT {{ $branding->nit }}<br>
                        +18 · Juega con responsabilidad.
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>