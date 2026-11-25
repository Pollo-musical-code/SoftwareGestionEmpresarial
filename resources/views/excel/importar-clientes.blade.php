<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">Importar clientes desde Excel</h2></x-slot>
    <div class="mx-auto max-w-3xl space-y-4 py-8 px-4">
        <div class="rounded-lg bg-white p-6 shadow">
            <p class="mb-3 text-sm text-gray-600">Columnas: <code>documento, nombre, fecha_nacimiento, nivel_vip</code>. Solo mayores de 18 años y sin documentos repetidos. El saldo de fichas siempre inicia en 0 (nunca se importa).
                <a class="text-amber-700 underline" href="{{ route('excel.clientes.plantilla') }}">Descargar plantilla</a></p>
            @error('archivo')<p class="mb-2 text-sm text-red-600">{{ $message }}</p>@enderror
            <form method="POST" action="{{ route('excel.clientes.importar.guardar') }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
                @csrf
                <input type="file" name="archivo" accept=".xlsx,.xls,.csv" required class="text-sm">
                <button class="rounded bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">Importar</button>
            </form>
        </div>

        @isset($creados)
            <div class="rounded-lg bg-white p-6 shadow">
                <p class="font-semibold text-green-700">✔ {{ $creados }} cliente(s) importado(s).</p>
                @if($fallos->isNotEmpty())
                    <p class="mt-3 font-semibold text-red-700">✖ {{ $fallos->count() }} error(es) — esas filas no se importaron:</p>
                    <ul class="mt-2 list-disc pl-6 text-sm">
                        @foreach($fallos as $f)
                            <li>Fila {{ $f->row() }} — <strong>{{ $f->attribute() }}</strong>: {{ implode(' ', $f->errors()) }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endisset
    </div>
</x-app-layout>
