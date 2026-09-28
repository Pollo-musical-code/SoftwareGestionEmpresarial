<div class="shrink-0 flex items-center">
    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-red-700">
        🎰 Casino Fortuna
    </a>
</div>

<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        Dashboard
    </x-nav-link>
    <x-nav-link :href="route('mesas.index')" :active="request()->routeIs('mesas.*')">
        Mesas
    </x-nav-link>
</div>