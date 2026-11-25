<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">{{ $usuario ? 'Editar usuario' : 'Nuevo usuario' }}</h2></x-slot>
    @php
        $rolesUsuario = old('roles', $usuario?->roles->pluck('name')->all() ?? []);
        $directos = old('permisos', $usuario?->permissions->pluck('name')->all() ?? []);
    @endphp
    <div class="mx-auto max-w-4xl py-8 px-4">
        @if($errors->any())<div class="mb-4 rounded bg-red-100 p-3 text-sm text-red-800"><ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        <form method="POST" action="{{ $usuario ? route('settings.users.update', $usuario) : route('settings.users.store') }}" class="space-y-6 rounded-lg bg-white p-6 shadow">
            @csrf @if($usuario) @method('PUT') @endif
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="text-sm">Nombre<input name="name" value="{{ old('name', $usuario?->name) }}" required class="mt-1 w-full rounded border-gray-300"></label>
                <label class="text-sm">Correo<input type="email" name="email" value="{{ old('email', $usuario?->email) }}" required class="mt-1 w-full rounded border-gray-300"></label>
                <label class="text-sm">Contraseña {{ $usuario ? '(dejar vacío para no cambiarla)' : '' }}<input type="password" name="password" {{ $usuario ? '' : 'required' }} class="mt-1 w-full rounded border-gray-300"></label>
                <label class="flex items-center gap-2 pt-6 text-sm"><input type="checkbox" name="active" value="1" @checked(old('active', $usuario ? $usuario->active : true))> Usuario activo</label>
            </div>

            <fieldset>
                <legend class="mb-2 font-semibold">Roles (puede tener varios)</legend>
                <div class="flex flex-wrap gap-3">
                    @foreach($roles as $rol)
                        <label class="flex items-center gap-2 rounded border px-3 py-2 text-sm"><input type="checkbox" name="roles[]" value="{{ $rol->name }}" @checked(in_array($rol->name, $rolesUsuario))> {{ $rol->name }}</label>
                    @endforeach
                </div>
            </fieldset>

            <fieldset>
                <legend class="mb-1 font-semibold">Permisos directos (extra al rol)</legend>
                <p class="mb-2 text-xs text-gray-500">Se suman a los del rol activo. Úsalos solo para excepciones.</p>
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach($permisos as $modulo => $lista)
                        <div class="rounded border p-3">
                            <p class="mb-1 text-sm font-medium">{{ \App\Support\PermissionCatalog::moduleLabel($modulo) }}</p>
                            @foreach($lista as $p)
                                <label class="mr-3 inline-flex items-center gap-1 text-xs"><input type="checkbox" name="permisos[]" value="{{ $p->name }}" @checked(in_array($p->name, $directos))> {{ \App\Support\PermissionCatalog::actionLabel(\App\Support\PermissionCatalog::split($p->name)[0]) }}</label>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </fieldset>

            <div class="flex gap-3">
                <button class="rounded bg-amber-600 px-5 py-2 font-semibold text-white hover:bg-amber-700">Guardar</button>
                <a href="{{ route('settings.users.index') }}" class="rounded border px-5 py-2">Cancelar</a>
            </div>
        </form>
    </div>
</x-app-layout>
