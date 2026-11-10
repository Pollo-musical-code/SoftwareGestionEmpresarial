<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ajustar inventario: {{ $ficha->denominacion }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('error'))
                <div class="p-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('inventario.ajustar.guardar', $ficha) }}" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4 border-t-4 border-amber-500">
                @csrf

                <p class="text-sm text-gray-700">Stock actual en el sistema: <strong>{{ $ficha->stock }}</strong></p>

                <div>
                    <x-input-label for="stock_contado" value="Cantidad contada en caja" />
                    <x-text-input id="stock_contado" name="stock_contado" type="number" min="0" class="mt-1 block w-full" :value="old('stock_contado')" required />
                    <x-input-error class="mt-2" :messages="$errors->get('stock_contado')" />
                </div>

                <div>
                    <x-input-label for="motivo" value="Motivo del ajuste (obligatorio, mínimo 10 caracteres)" />
                    <textarea id="motivo" name="motivo" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>{{ old('motivo') }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('motivo')" />
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>Registrar ajuste</x-primary-button>
                    <a href="{{ route('inventario.kardex', ['ficha' => $ficha->id]) }}" class="underline">Cancelar</a>
                </div>
            </form>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-2">Últimos movimientos</h3>
                <ul class="text-sm space-y-1">
                    @forelse ($ultimos as $m)
                        <li>{{ $m->created_at->format('d/m/Y H:i') }} · {{ $m->etiquetaConcepto() }} · {{ $m->tipo }} de {{ $m->cantidad }} · saldo {{ $m->saldo }}</li>
                    @empty
                        <li class="text-gray-500">Sin movimientos.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>