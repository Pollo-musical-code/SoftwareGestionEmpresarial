<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mesas de juego
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden border-t-4 border-amber-500">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Juego</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Apuesta mín.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Apuesta máx.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($mesas as $mesa)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $mesa->id }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $mesa->juego->nombre }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">${{ number_format($mesa->juego->apuesta_min, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">${{ number_format($mesa->juego->apuesta_max, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-1 rounded-full text-xs {{ $mesa->estado === 'abierta' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ ucfirst($mesa->estado) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay mesas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>