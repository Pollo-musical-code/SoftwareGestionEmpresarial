<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">Usuarios</h2></x-slot>
    <div class="mx-auto max-w-6xl space-y-4 py-8 px-4">
        @if(session('status'))<div class="rounded bg-green-100 p-3 text-sm text-green-800">{{ session('status') }}</div>@endif
        @error('regla')<div class="rounded bg-red-100 p-3 text-sm text-red-800">{{ $message }}</div>@enderror

        <div class="flex flex-wrap items-center justify-between gap-3">
            <form method="GET" class="flex flex-wrap gap-2">
                <input name="q" value="{{ request('q') }}" placeholder="Buscar nombre o correo" class="rounded border-gray-300 text-sm">
                <select name="estado" class="rounded border-gray-300 text-sm">
                    @foreach(['todos' => 'Todos', 'activos' => 'Activos', 'inactivos' => 'Inactivos'] as $k => $v)
                        <option value="{{ $k }}" @selected($estado === $k)>{{ $v }}</option>
                    @endforeach
                </select>
                <button class="rounded bg-gray-800 px-3 py-1 text-sm text-white">Filtrar</button>
            </form>
            <a href="{{ route('settings.users.create') }}" class="rounded bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">+ Nuevo usuario</a>
        </div>

        <div class="overflow-x-auto rounded-lg bg-white shadow">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-left text-xs uppercase text-gray-600">
                    <tr><th class="p-3">Nombre</th><th class="p-3">Correo</th><th class="p-3">Roles</th><th class="p-3">Estado</th><th class="p-3"></th></tr>
                </thead>
                <tbody>
                @foreach($usuarios as $u)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $u->name }}</td>
                        <td class="p-3">{{ $u->email }}</td>
                        <td class="p-3">@foreach($u->roles as $r)<span class="mr-1 rounded bg-amber-100 px-2 py-0.5 text-xs text-amber-800">{{ $r->name }}</span>@endforeach</td>
                        <td class="p-3">
                            <span class="rounded px-2 py-0.5 text-xs {{ $u->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $u->active ? 'Activo' : 'Inactivo' }}</span>
                        </td>
                        <td class="flex gap-2 p-3">
                            <a href="{{ route('settings.users.edit', $u) }}" class="text-amber-700 hover:underline">Editar</a>
                            <form method="POST" action="{{ route('settings.users.toggle', $u) }}">@csrf @method('PATCH')
                                <button class="text-gray-700 hover:underline">{{ $u->active ? 'Desactivar' : 'Activar' }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $usuarios->links() }}
    </div>
</x-app-layout>
