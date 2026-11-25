<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">Roles</h2></x-slot>
    <div class="mx-auto max-w-4xl space-y-4 py-8 px-4">
        @if(session('status'))<div class="rounded bg-green-100 p-3 text-sm text-green-800">{{ session('status') }}</div>@endif
        @error('regla')<div class="rounded bg-red-100 p-3 text-sm text-red-800">{{ $message }}</div>@enderror
        <div class="flex justify-end"><a href="{{ route('settings.roles.create') }}" class="rounded bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">+ Nuevo rol</a></div>
        <div class="overflow-x-auto rounded-lg bg-white shadow">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-left text-xs uppercase text-gray-600"><tr><th class="p-3">Rol</th><th class="p-3">Usuarios</th><th class="p-3">Permisos</th><th class="p-3"></th></tr></thead>
                <tbody>
                @foreach($roles as $r)
                    <tr class="border-t">
                        <td class="p-3 font-medium capitalize">{{ $r->name }}</td>
                        <td class="p-3">{{ $r->users_count }}</td>
                        <td class="p-3">{{ $r->permissions_count }}</td>
                        <td class="flex gap-3 p-3">
                            <a href="{{ route('settings.roles.edit', $r) }}" class="text-amber-700 hover:underline">Editar</a>
                            <form method="POST" action="{{ route('settings.roles.destroy', $r) }}" onsubmit="return confirm('¿Eliminar el rol {{ $r->name }}?')">@csrf @method('DELETE')
                                <button class="text-red-700 hover:underline">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
