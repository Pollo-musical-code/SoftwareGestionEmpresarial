@auth
<div class="mt-4 space-y-1 border-t border-white/10 pt-4">
    @if(auth()->user()->hasMultipleRoles())
        <a href="{{ route('rol-activo.elegir') }}" class="block px-4 py-2 text-sm text-amber-300 hover:text-amber-200">
            🎭 Rol activo: <strong>{{ auth()->user()->activeRole() ?? 'elegir' }}</strong>
        </a>
    @endif

    @if(Route::has('notificaciones.index'))
        <a href="{{ route('notificaciones.index') }}" class="flex items-center justify-between px-4 py-2 text-sm text-amber-300 hover:text-amber-200">
            <span>🔔 Notificaciones</span>
            @php($sinLeer = auth()->user()->unreadNotifications()->count())
            @if($sinLeer > 0)
                <span class="rounded-full bg-red-600 px-2 text-xs font-bold text-white">{{ $sinLeer }}</span>
            @endif
        </a>
    @endif

    @can('gestionar-usuarios')
        <a href="{{ route('settings.users.index') }}" class="block px-4 py-2 text-sm text-amber-300 hover:text-amber-200">👥 Usuarios</a>
    @endcan
    @can('gestionar-roles')
        <a href="{{ route('settings.roles.index') }}" class="block px-4 py-2 text-sm text-amber-300 hover:text-amber-200">🛡️ Roles</a>
        <a href="{{ route('settings.permissions.index') }}" class="block px-4 py-2 text-sm text-amber-300 hover:text-amber-200">🧩 Matriz de permisos</a>
    @endcan

    @if(Route::has('excel.clientes.exportar'))
        @can('exportar-clientes')
            <a href="{{ route('excel.clientes.exportar') }}" class="block px-4 py-2 text-sm text-amber-300 hover:text-amber-200">📤 Exportar clientes</a>
        @endcan
        @can('exportar-fichas')
            <a href="{{ route('excel.fichas.exportar') }}" class="block px-4 py-2 text-sm text-amber-300 hover:text-amber-200">📤 Exportar fichas</a>
        @endcan
        @can('exportar-ventas')
            <a href="{{ route('excel.ventas.exportar') }}" class="block px-4 py-2 text-sm text-amber-300 hover:text-amber-200">📤 Exportar ventas</a>
        @endcan
        @can('importar-clientes')
            <a href="{{ route('excel.clientes.importar') }}" class="block px-4 py-2 text-sm text-amber-300 hover:text-amber-200">📥 Importar clientes</a>
        @endcan
    @endif
</div>
@endauth
