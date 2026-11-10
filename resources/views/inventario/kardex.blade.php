<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kardex de inventario</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="p-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
            @endif

            <form method="GET" action="{{ route('inventario.kardex') }}" class="bg-white shadow-sm sm:rounded-lg p-4 flex flex-wrap items-end gap-3 border-t-4 border-amber-500">
                <div>
                    <x-input-label for="ficha" value="Ficha" />
                    <select id="ficha" name="ficha" class="mt-1 border-gray-300 rounded-md" required>
                        <option value="">Seleccione una ficha</option>
                        @foreach ($fichas as $f)
                            <option value="{{ $f->id }}" @selected($ficha?->id === $f->id)>{{ $f->denominacion }} (stock {{ $f->stock }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="concepto" value="Concepto" />
                    <select id="concepto" name="concepto" class="mt-1 border-gray-300 rounded-md">
                        <option value="">Todos</option>
                        @foreach ($conceptos as $clave => $etiqueta)
                            <option value="{{ $clave }}" @selected(request('concepto') === $clave)>{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="from" value="Desde" />
                    <input id="from" type="date" name="from" value="{{ request('from') }}" class="mt-1 border-gray-300 rounded-md">
                </div>
                <div>
                    <x-input-label for="to" value="Hasta" />
                    <input id="to" type="date" name="to" value="{{ request('to') }}" class="mt-1 border-gray-300 rounded-md">
                </div>
                <x-primary-button>Ver kardex</x-primary-button>
            </form>

            @if ($ficha)
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white shadow-sm rounded-lg p-4 border-t-4 border-green-600">
                        <p class="text-sm text-gray-500">Entradas</p>
                        <p class="text-2xl font-bold">{{ $totales['entradas'] }}</p>
                    </div>
                    <div class="bg-white shadow-sm rounded-lg p-4 border-t-4 border-red-600">
                        <p class="text-sm text-gray-500">Salidas</p>
                        <p class="text-2xl font-bold">{{ $totales['salidas'] }}</p>
                    </div>
                    <div class="bg-white shadow-sm rounded-lg p-4 border-t-4 border-gray-800">
                        <p class="text-sm text-gray-500">Stock actual</p>
                        <p class="text-2xl font-bold">{{ $ficha->stock }}</p>
                    </div>
                </div>

                @if ($totales['cuadra'])
                    <div class="p-3 rounded bg-green-100 text-green-800">✓ El saldo del kardex coincide con el stock de la ficha.</div>
                @else
                    <div class="p-3 rounded bg-red-100 text-red-800">⚠ El saldo del kardex NO coincide con el stock. Algo se modificó fuera del proceso (ejecute kardex:inicializar o revise la base de datos).</div>
                @endif

                <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('inventario.pdf', array_merge(['ficha' => $ficha->id], request()->only('from', 'to', 'concepto'))) }}" target="_blank" class="text-blue-700 underline">Descargar PDF</a>
                        @can('ajustar-inventario')
                            <a href="{{ route('inventario.ajustar', $ficha->id) }}" class="text-amber-700 underline">Ajustar inventario</a>
                        @endcan
                    </div>

                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b text-left">
                                <th class="p-2">Fecha</th>
                                <th class="p-2">Concepto</th>
                                <th class="p-2">Documento</th>
                                <th class="p-2 text-right">Entrada</th>
                                <th class="p-2 text-right">Salida</th>
                                <th class="p-2 text-right">Saldo</th>
                                <th class="p-2">Usuario</th>
                                <th class="p-2">Notas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($movimientos as $m)
                                @php $doc = $m->etiquetaReferencia(); @endphp
                                <tr class="border-b">
                                    <td class="p-2 whitespace-nowrap">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="p-2">{{ $m->etiquetaConcepto() }}</td>
                                    <td class="p-2">
                                        @if ($doc && $m->referencia instanceof \App\Models\Venta && ! $m->referencia->trashed() && auth()->user()->can('ver-ventas'))
                                            <a href="{{ route('ventas.show', $m->referencia) }}" class="text-blue-700 underline">{{ $doc }}</a>
                                        @elseif ($doc && $m->referencia instanceof \App\Models\Compra && ! $m->referencia->trashed() && auth()->user()->can('ver-compras'))
                                            <a href="{{ route('compras.show', $m->referencia) }}" class="text-blue-700 underline">{{ $doc }}</a>
                                        @else
                                            {{ $doc ?? '—' }}
                                        @endif
                                    </td>
                                    <td class="p-2 text-right text-green-700">{{ $m->tipo === 'entrada' ? $m->cantidad : '' }}</td>
                                    <td class="p-2 text-right text-red-700">{{ $m->tipo === 'salida' ? $m->cantidad : '' }}</td>
                                    <td class="p-2 text-right font-semibold">{{ $m->saldo }}</td>
                                    <td class="p-2">{{ $m->user?->name ?? 'Sistema' }}</td>
                                    <td class="p-2 text-xs text-gray-600">{{ $m->notas }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="p-4 text-center text-gray-500">No hay movimientos para mostrar.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $movimientos->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>