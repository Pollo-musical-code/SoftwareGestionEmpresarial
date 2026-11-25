<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-xl font-bold text-ink">Identidad del casino</h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 rounded-lg bg-green-100 p-3 text-green-800">{{ session('success') }}</div>
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

            <div x-data="{
                    color: @js(old('primary_color', $company->primary_color)),
                    nombre: @js(old('trade_name', $company->trade_name)),
                    eslogan: @js(old('slogan', $company->slogan)),
                }" class="grid gap-6 lg:grid-cols-3">

                <form method="POST" action="{{ route('settings.branding.update') }}" enctype="multipart/form-data"
                      class="space-y-6 rounded-xl border-t-4 border-gold bg-white p-6 shadow-sm lg:col-span-2">
                    @csrf
                    @method('PUT')

                    <div>
                        <h3 class="font-semibold text-slate-800">Datos del casino</h3>
                        <p class="text-xs text-slate-400">Aparecen en el menú, el inicio de sesión y los documentos PDF.</p>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="trade_name" value="Nombre comercial" />
                            <x-text-input id="trade_name" name="trade_name" type="text" class="mt-1 block w-full" x-model="nombre" :value="old('trade_name', $company->trade_name)" required />
                        </div>
                        <div>
                            <x-input-label for="legal_name" value="Razón social" />
                            <x-text-input id="legal_name" name="legal_name" type="text" class="mt-1 block w-full" :value="old('legal_name', $company->legal_name)" required />
                        </div>
                        <div>
                            <x-input-label for="nit" value="NIT" />
                            <x-text-input id="nit" name="nit" type="text" class="mt-1 block w-full" :value="old('nit', $company->nit)" required />
                        </div>
                        <div>
                            <x-input-label for="slogan" value="Eslogan (opcional)" />
                            <x-text-input id="slogan" name="slogan" type="text" class="mt-1 block w-full" x-model="eslogan" :value="old('slogan', $company->slogan)" />
                        </div>
                        <div>
                            <x-input-label for="address" value="Dirección (opcional)" />
                            <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $company->address)" />
                        </div>
                        <div>
                            <x-input-label for="city" value="Ciudad (opcional)" />
                            <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $company->city)" />
                        </div>
                        <div>
                            <x-input-label for="phone" value="Teléfono (opcional)" />
                            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $company->phone)" />
                        </div>
                        <div>
                            <x-input-label for="email" value="Correo (opcional)" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $company->email)" />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="website" value="Sitio web (opcional)" />
                            <x-text-input id="website" name="website" type="text" class="mt-1 block w-full" :value="old('website', $company->website)" />
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold text-slate-800">Color de marca</h3>
                        <p class="mb-3 text-xs text-slate-400">Cambia los botones, el menú activo y los gráficos de todo el sistema.</p>

                        <div class="flex flex-wrap items-center gap-3">
                            @foreach ($palette as $nombre => $hex)
                                <button type="button" @click="color = '{{ $hex }}'" title="{{ $nombre }}"
                                        class="h-9 w-9 rounded-full ring-2 ring-offset-2 transition"
                                        :class="color.toLowerCase() === '{{ $hex }}' ? 'ring-gold' : 'ring-transparent'"
                                        style="background-color: {{ $hex }}"></button>
                            @endforeach
                            <input type="color" x-model="color" class="h-9 w-12 cursor-pointer rounded border border-slate-300 bg-white p-0.5" aria-label="Elegir otro color">
                            <input type="text" id="primary_color" name="primary_color" x-model="color" maxlength="7"
                                   class="w-28 rounded-lg border-slate-300 font-mono text-sm shadow-sm focus:border-brand focus:ring-brand" required>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold text-slate-800">Logo</h3>
                        <p class="mb-3 text-xs text-slate-400">PNG, JPG o WEBP de máximo 1 MB. Si no subes uno, se usa una ficha con tus iniciales.</p>

                        <div class="flex flex-wrap items-center gap-4">
                            <x-brand-logo class="h-16 w-16" />
                            <input type="file" name="logo" accept="image/png,image/jpeg,image/webp" class="text-sm">
                            @if ($company->logo_path)
                                <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                                    <input type="checkbox" name="remove_logo" value="1" class="rounded border-slate-300 text-brand focus:ring-brand">
                                    Quitar el logo actual
                                </label>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        @can('editar-branding')
                            <x-primary-button>Guardar identidad</x-primary-button>
                        @endcan
                        <a href="{{ route('dashboard') }}" class="text-sm underline">Cancelar</a>
                    </div>
                </form>

                {{-- Vista previa en vivo --}}
                <div class="space-y-3 lg:sticky lg:top-24 lg:self-start">
                    <h3 class="font-semibold text-slate-800">Así se verá</h3>
                    <div class="overflow-hidden rounded-xl border border-slate-200 shadow-sm">
                        <div class="flex items-center gap-3 bg-ink p-4">
                            <span class="flex h-11 w-11 items-center justify-center rounded-full text-lg font-bold text-white ring-2 ring-gold" :style="`background-color: ${color}`">♠</span>
                            <div class="min-w-0">
                                <p class="truncate font-display text-lg font-bold text-gold" x-text="nombre"></p>
                                <p class="truncate text-xs text-slate-400" x-text="eslogan"></p>
                            </div>
                        </div>
                        <div class="space-y-3 bg-white p-4">
                            <div class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-white" :style="`background-color: ${color}`">
                                Opción de menú activa
                            </div>
                            <button type="button" class="rounded-lg px-4 py-2 text-sm font-semibold text-white" :style="`background-color: ${color}`">Botón principal</button>
                            <div class="h-2 rounded-full" :style="`background-color: ${color}`"></div>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400">El eslogan y el nombre se actualizan mientras escribes. El logo se ve al guardar.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
