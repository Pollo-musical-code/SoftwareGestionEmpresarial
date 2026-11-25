<x-guest-layout>
    <div class="mb-6">
        <h2 class="font-display text-2xl font-bold text-ink">Crear cuenta</h2>
        <p class="mt-1 text-sm text-slate-500">Regístrate para usar el sistema de gestión de {{ $branding->trade_name }}.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="name" value="Nombre" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" class="mt-1 block w-full" required autofocus />
        </div>

        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" class="mt-1 block w-full" required />
        </div>

        <div>
            <x-input-label for="password" value="Contraseña" />
            <x-text-input id="password" type="password" name="password" class="mt-1 block w-full" required autocomplete="new-password" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirmar contraseña" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" class="mt-1 block w-full" required />
        </div>

        <div class="flex items-center justify-between pt-2">
            <a class="text-sm text-slate-600 underline hover:text-slate-900" href="{{ route('login') }}">¿Ya tienes una cuenta?</a>
            <x-primary-button>Registrarme</x-primary-button>
        </div>
    </form>
</x-guest-layout>