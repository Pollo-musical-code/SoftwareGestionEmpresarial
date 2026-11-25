<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">Matriz de permisos</h2></x-slot>
    <div class="mx-auto max-w-6xl space-y-4 py-8 px-4">
        @if(session('status'))<div class="rounded bg-green-100 p-3 text-sm text-green-800">{{ session('status') }}</div>@endif
        <p class="text-sm text-gray-600">Marca qué puede hacer cada rol. La columna del administrador está bloqueada: siempre tiene todo.</p>
        <form method="POST" action="{{ route('settings.permissions.update') }}" class="overflow-x-auto rounded-lg bg-white shadow">
            @csrf @method('PUT')
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-xs uppercase text-gray-600">
                    <tr>
                        <th class="p-3 text-left">Permiso</th>
                        @foreach($roles as $r)<th class="p-3 text-center capitalize">{{ $r->name }}</th>@endforeach
                    </tr>
                </thead>
                <tbody>
                @foreach($grupos as $modulo => $lista)
                    <tr class="bg-amber-50"><td colspan="{{ $roles->count() + 1 }}" class="p-2 font-semibold">{{ \App\Support\PermissionCatalog::moduleLabel($modulo) }}</td></tr>
                    @foreach($lista as $p)
                        <tr class="border-t">
                            <td class="p-2 pl-6">{{ \App\Support\PermissionCatalog::actionLabel(\App\Support\PermissionCatalog::split($p->name)[0]) }} <span class="text-xs text-gray-400">({{ $p->name }})</span></td>
                            @foreach($roles as $r)
                                @php($es = \App\Support\PermissionCatalog::isSuperRole($r->name))
                                <td class="p-2 text-center">
                                    <input type="checkbox" name="matriz[{{ $r->id }}][]" value="{{ $p->name }}" @checked($es || $r->permissions->contains('name', $p->name)) {{ $es ? 'disabled' : '' }}>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
            <div class="p-4"><button class="rounded bg-amber-600 px-5 py-2 font-semibold text-white hover:bg-amber-700">Guardar matriz</button></div>
        </form>
    </div>
</x-app-layout>
