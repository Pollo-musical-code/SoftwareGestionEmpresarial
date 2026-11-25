{{--
    Enlace del menú lateral con ícono.
    Uso: <x-nav-link :href="route('mesas.index')" :active="request()->routeIs('mesas.*')" icon="table">Mesas</x-nav-link>
--}}
@props(['active' => false, 'icon' => 'dot'])

@php
    $classes = $active
        ? 'group flex items-center gap-3 rounded-lg bg-brand px-3 py-2 text-sm font-semibold text-white shadow-md shadow-black/30 ring-1 ring-gold/40'
        : 'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-slate-300 transition hover:bg-white/5 hover:text-white';
    $iconClasses = $active ? 'h-5 w-5 shrink-0 text-gold-light' : 'h-5 w-5 shrink-0 text-gold/70 group-hover:text-gold';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} @if ($active) aria-current="page" @endif>
    <x-icon :name="$icon" :class="$iconClasses" />
    <span class="truncate">{{ $slot }}</span>
</a>