<x-guest-layout>
    <div class="mb-6">
        <h2 class="font-display text-2xl font-bold text-ink">Iniciar sesión</h2>
        <p class="mt-1 text-sm text-slate-500">Ingresa con tu correo y contraseña para entrar al sistema.</p>
    </div>

    @if (session('status'))
        <div class="mb-4 text-sm font-medium text-green-600">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" class="mt-1 block w-full" required autofocus />
        </div>

        <div>
            <x-input-label for="password" value="Contraseña" />
            <x-text-input id="password" type="password" name="password" class="mt-1 block w-full" required autocomplete="current-password" />
        </div>

        <label for="remember_me" class="inline-flex items-center">
            <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-300 text-brand focus:ring-brand">
            <span class="ms-2 text-sm text-slate-600">Recordarme</span>
        </label>

        <div class="flex items-center justify-between pt-2">
            @if (Route::has('password.request'))
                <a class="text-sm text-slate-600 underline hover:text-slate-900" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
            @endif
            <x-primary-button class="ms-auto">Entrar</x-primary-button>
        </div>
    </form>
</x-guest-layout>