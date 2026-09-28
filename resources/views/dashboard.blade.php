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
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-5 border-t-4 border-red-600">
                    <p class="text-sm text-gray-500">Mesas activas</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $mesasActivas }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5 border-t-4 border-amber-500">
                    <p class="text-sm text-gray-500">Clientes registrados</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalClientes }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5 border-t-4 border-green-600">
                    <p class="text-sm text-gray-500">Apuestas hoy</p>
                    <p class="text-3xl font-bold text-gray-800">--</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5 border-t-4 border-gray-800">
                    <p class="text-sm text-gray-500">Fichas en circulación</p>
                    <p class="text-3xl font-bold text-gray-800">${{ number_format($fichasCirculacion, 0, ',', '.') }}</p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>