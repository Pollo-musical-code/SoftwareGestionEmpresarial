<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">Elige tu rol activo</h2></x-slot>
    <div class="mx-auto max-w-lg py-10 px-4">
        <div class="rounded-lg bg-white p-6 shadow">
            <p class="mb-4 text-sm text-gray-600">Tu usuario tiene varios roles. Trabajarás con <strong>uno a la vez</strong>; tus permisos serán los de ese rol (más los permisos directos que tengas). Puedes cambiarlo cuando quieras.</p>
            @error('rol')<p class="mb-3 text-sm text-red-600">{{ $message }}</p>@enderror
            <form method="POST" action="{{ route('rol-activo.guardar') }}" class="space-y-3">
                @csrf
                @foreach($roles as $rol)
                    <label class="flex cursor-pointer items-center gap-3 rounded border p-3 hover:bg-amber-50">
                        <input type="radio" name="rol" value="{{ $rol }}" @checked($actual === $rol || ($loop->first && !$actual)) required>
                        <span class="font-medium capitalize">{{ $rol }}</span>
                    </label>
                @endforeach
                <button class="w-full rounded bg-amber-600 px-4 py-2 font-semibold text-white hover:bg-amber-700">Entrar con este rol</button>
            </form>
        </div>
    </div>
</x-app-layout>
