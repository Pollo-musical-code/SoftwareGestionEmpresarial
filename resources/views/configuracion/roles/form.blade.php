<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">{{ $rol ? 'Editar rol' : 'Nuevo rol' }}</h2></x-slot>
    @php
        $marcados = old('permisos', $rol?->permissions->pluck('name')->all() ?? []);
        $esAdmin = $rol && \App\Support\PermissionCatalog::isSuperRole($rol->name);
    @endphp
    <div class="mx-auto max-w-4xl py-8 px-4">
        @if($errors->any())<div class="mb-4 rounded bg-red-100 p-3 text-sm text-red-800"><ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        <form method="POST" action="{{ $rol ? route('settings.roles.update', $rol) : route('settings.roles.store') }}" class="space-y-6 rounded-lg bg-white p-6 shadow">
            @csrf @if($rol) @method('PUT') @endif
            <label class="block text-sm">Nombre del rol<input name="name" value="{{ old('name', $rol?->name) }}" required {{ $esAdmin ? 'readonly' : '' }} class="mt-1 w-full rounded border-gray-300"></label>
            @if($esAdmin)<p class="rounded bg-amber-50 p-2 text-xs text-amber-800">El administrador siempre conserva todos los permisos.</p>@endif
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach($permisos as $modulo => $lista)
                    <div class="rounded border p-3">
                        <p class="mb-1 text-sm font-medium">{{ \App\Support\PermissionCatalog::moduleLabel($modulo) }}</p>
                        @foreach($lista as $p)
                            <label class="mr-3 inline-flex items-center gap-1 text-xs"><input type="checkbox" name="permisos[]" value="{{ $p->name }}" @checked($esAdmin || in_array($p->name, $marcados)) {{ $esAdmin ? 'disabled' : '' }}> {{ \App\Support\PermissionCatalog::actionLabel(\App\Support\PermissionCatalog::split($p->name)[0]) }}</label>
                        @endforeach
                    </div>
                @endforeach
            </div>
            <div class="flex gap-3">
                <button class="rounded bg-amber-600 px-5 py-2 font-semibold text-white hover:bg-amber-700">Guardar</button>
                <a href="{{ route('settings.roles.index') }}" class="rounded border px-5 py-2">Cancelar</a>
            </div>
        </form>
    </div>
</x-app-layout>
