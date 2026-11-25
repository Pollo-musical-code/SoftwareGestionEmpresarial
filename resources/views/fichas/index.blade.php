<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-xl font-bold text-ink">Fichas en caja</h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl border-t-4 border-gold bg-white p-6 shadow-sm">
                <p class="mb-4 text-sm text-slate-500">Cuántas fichas quedan de cada denominación. Las que tienen pocas unidades aparecen marcadas.</p>

                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-slate-500">
                            <th class="p-2">Ficha</th>
                            <th class="p-2">Valor</th>
                            <th class="p-2">Stock disponible</th>
                            <th class="p-2">Estado</th>
                            <th class="p-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($fichas as $ficha)
                            <tr class="border-b">
                                <td class="p-2">
                                    <div class="flex items-center gap-3">
                                        <x-chip :valor="$ficha->valor" size="md" />
                                        <span class="font-medium">{{ $ficha->denominacion }}</span>
                                    </div>
                                </td>
                                <td class="p-2">${{ number_format((float) $ficha->valor, 0, ',', '.') }}</td>
                                <td class="p-2">
                                    <span class="text-lg font-semibold">{{ $ficha->stock }}</span>
                                    @if ($ficha->stock <= 5)
                                        <span class="ms-2 rounded-full bg-rose-100 px-2 py-1 text-xs font-semibold text-rose-700">Stock bajo</span>
                                    @endif
                                </td>
                                <td class="p-2">{{ $ficha->activo ? 'Activa' : 'Inactiva' }}</td>
                                <td class="space-x-3 p-2">
                                    @can('ver-inventario')
                                        <a href="{{ route('inventario.kardex', ['ficha' => $ficha->id]) }}" class="text-blue-700 underline">Kardex</a>
                                    @endcan
                                    @can('ajustar-inventario')
                                        <a href="{{ route('inventario.ajustar', $ficha) }}" class="text-amber-700 underline">Ajustar</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-4 text-center text-slate-500">No hay fichas registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>