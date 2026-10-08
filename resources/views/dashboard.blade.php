<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Panel de control
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border-l-4 border-amber-500">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold">¡Bienvenido, {{ auth()->user()->name }}!</h1>
                    <p class="text-gray-600">Casino Fortuna - Sistema de Gestión</p>
                    <p class="text-sm text-gray-500 mt-1">
                        Rol: {{ $roles->isNotEmpty() ? $roles->join(', ') : 'Sin rol asignado' }}
                    </p>
                </div>
            </div>

            @if ($mesasActivas !== null || $totalClientes !== null)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    @if ($mesasActivas !== null)
                        <div class="bg-white shadow-sm rounded-lg p-5 border-t-4 border-red-600">
                            <p class="text-sm text-gray-500">Mesas activas</p>
                            <p class="text-3xl font-bold text-gray-800">{{ $mesasActivas }}</p>
                        </div>
                    @endif
                    @if ($totalClientes !== null)
                        <div class="bg-white shadow-sm rounded-lg p-5 border-t-4 border-amber-500">
                            <p class="text-sm text-gray-500">Clientes registrados</p>
                            <p class="text-3xl font-bold text-gray-800">{{ $totalClientes }}</p>
                        </div>
                        <div class="bg-white shadow-sm rounded-lg p-5 border-t-4 border-gray-800">
                            <p class="text-sm text-gray-500">Fichas en circulación</p>
                            <p class="text-3xl font-bold text-gray-800">${{ number_format($fichasCirculacion, 0, ',', '.') }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-3">Módulos disponibles</h3>
                <ul class="list-disc ps-6 space-y-1">
                    @forelse ($modules as $module)
                        <li>
                            <a href="{{ route($module['route']) }}" class="text-amber-700 underline">{{ $module['title'] }}</a>
                        </li>
                    @empty
                        <li class="text-gray-500">No tienes módulos disponibles. Pide a un administrador que te asigne un rol.</li>
                    @endforelse
                </ul>
            </div>

        </div>
    </div>
</x-app-layout>