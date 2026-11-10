<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Auditoría</h2>
    </x-slot>

    @php
        $coloresEvento = [
            'created' => 'bg-green-100 text-green-800',
            'updated' => 'bg-blue-100 text-blue-800',
            'deleted' => 'bg-red-100 text-red-800',
            'restored' => 'bg-amber-100 text-amber-800',
        ];
        // Convierte cualquier valor guardado a texto legible
        $mostrar = function ($v) {
            if (is_bool($v)) return $v ? 'Sí' : 'No';
            if (is_null($v)) return '—';
            if (is_array($v)) return implode(', ', array_map(fn ($x) => is_scalar($x) ? $x : json_encode($x), $v)) ?: '—';
            return (string) $v;
        };
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4 border-t-4 border-amber-500">

                <form method="GET" action="{{ route('auditoria.index') }}" class="flex flex-wrap items-end gap-3">
                    <div>
                        <x-input-label for="modulo" value="Módulo" />
                        <select id="modulo" name="modulo" class="mt-1 border-gray-300 rounded-md">
                            <option value="">Todos</option>
                            @foreach ($modulos as $m)
                                <option value="{{ $m }}" @selected(request('modulo') === $m)>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="evento" value="Evento" />
                        <select id="evento" name="evento" class="mt-1 border-gray-300 rounded-md">
                            <option value="">Todos</option>
                            @foreach ($eventos as $clave => $etiqueta)
                                <option value="{{ $clave }}" @selected(request('evento') === $clave)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="usuario" value="Usuario" />
                        <select id="usuario" name="usuario" class="mt-1 border-gray-300 rounded-md">
                            <option value="">Todos</option>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->id }}" @selected((int) request('usuario') === $u->id)>{{ $u->name }}</option>
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
                    <x-primary-button>Filtrar</x-primary-button>
                </form>

                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="p-2">Fecha</th>
                            <th class="p-2">Usuario</th>
                            <th class="p-2">Módulo</th>
                            <th class="p-2">Evento</th>
                            <th class="p-2">Registro</th>
                            <th class="p-2">Cambios (antes → después)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($actividades as $a)
                            @php
                                $nuevo = $a->attribute_changes?->get('attributes', []) ?? [];
                                $viejo = $a->attribute_changes?->get('old', []) ?? [];
                                $sujeto = $a->subject;
                                $nombre = $sujeto?->nombre ?? $sujeto?->denominacion ?? $sujeto?->numero_factura
                                    ?? $sujeto?->numero_compra ?? $sujeto?->name;
                            @endphp
                            <tr class="border-b align-top">
                                <td class="p-2 whitespace-nowrap">{{ $a->created_at->format('d/m/Y H:i:s') }}</td>
                                <td class="p-2">{{ $a->causer?->name ?? 'Sistema' }}</td>
                                <td class="p-2">{{ $a->log_name }}</td>
                                <td class="p-2">
                                    <span class="px-2 py-0.5 rounded-full text-xs {{ $coloresEvento[$a->event] ?? 'bg-gray-100' }}">{{ $a->description }}</span>
                                </td>
                                <td class="p-2">
                                    {{ class_basename($a->subject_type) }} #{{ $a->subject_id }}
                                    <span class="block text-xs text-gray-500">{{ $nombre }}</span>
                                </td>
                                <td class="p-2">
                                    <ul class="text-xs space-y-0.5">
                                        @foreach ($nuevo as $campo => $valor)
                                            <li>
                                                <strong>{{ $campo }}:</strong>
                                                @if (array_key_exists($campo, $viejo))
                                                    <span class="text-red-700 line-through">{{ $mostrar($viejo[$campo]) }}</span> →
                                                @endif
                                                <span class="text-green-700">{{ $mostrar($valor) }}</span>
                                            </li>
                                        @endforeach
                                        @if (empty($nuevo) && ! empty($viejo))
                                            @foreach ($viejo as $campo => $valor)
                                                <li><strong>{{ $campo }}:</strong> {{ $mostrar($valor) }}</li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-4 text-center text-gray-500">No hay actividad registrada.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $actividades->links() }}
            </div>
        </div>
    </div>
</x-app-layout>