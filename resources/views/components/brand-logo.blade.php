{{--
    Logo del casino: la imagen subida en Identidad corporativa o, si no hay,
    una ficha de casino con las iniciales del nombre comercial.
    Uso: <x-brand-logo class="h-12 w-12" />
--}}
@if ($branding->logoUrl())
    <img src="{{ $branding->logoUrl() }}" alt="Logo de {{ $branding->trade_name }}"
         {{ $attributes->merge(['class' => 'h-10 w-10 rounded-full bg-white object-contain p-0.5 ring-2 ring-gold']) }}>
@else
    <svg viewBox="0 0 100 100" role="img" aria-label="Logo de {{ $branding->trade_name }}"
         {{ $attributes->merge(['class' => 'h-10 w-10']) }}>
        <circle cx="50" cy="50" r="48" style="fill: rgb(var(--brand));" />
        <circle cx="50" cy="50" r="40" fill="none" stroke="#ffffff" stroke-width="7" stroke-dasharray="15 11" opacity="0.9" />
        <circle cx="50" cy="50" r="29" fill="#d4af37" />
        <text x="50" y="59" text-anchor="middle" font-family="Georgia, serif" font-size="26" font-weight="700" fill="#1a1207">{{ $branding->initials() }}</text>
    </svg>
@endif