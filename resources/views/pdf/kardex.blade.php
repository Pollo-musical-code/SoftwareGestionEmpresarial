<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Fichas en caja</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 border-t-4 border-amber-500">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="p-2">Denominación</th>
                            <th class="p-2">Valor</th>
                            <th class="p-2">Stock disponible</th>
                            <th class="p-2">Estado</th>
                            <th class="p-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($fichas as $ficha)
                            <tr class="border-b">
                                <td class="p-2 font-medium">{{ $ficha->denominacion }}</td>
                                <td class="p-2">${{ number_format((float) $ficha->valor, 0, ',', '.') }}</td>
                                <td class="p-2">
                                    {{ $ficha->stock }}
                                    @if ($ficha->stock <= 5)
                                        <span class="ms-2 px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">Stock bajo</span>
                                    @endif
                                </td>
                                <td class="p-2">{{ $ficha->activo ? 'Activa' : 'Inactiva' }}</td>
                                <td class="p-2 space-x-2">
                                    @can('ver-inventario')
                                        <a href="{{ route('inventario.kardex', ['ficha' => $ficha->id]) }}" class="text-blue-700 underline">Kardex</a>
                                    @endcan
                                    @can('ajustar-inventario')
                                        <a href="{{ route('inventario.ajustar', $ficha) }}" class="text-amber-700 underline">Ajustar</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-4 text-center text-gray-500">No hay fichas registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>