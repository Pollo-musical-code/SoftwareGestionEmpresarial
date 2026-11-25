{{--
    Ficha de casino con los colores reales según su valor.
    Uso: <x-chip :valor="$ficha->valor" size="md" />   (size: sm, md, lg)
--}}
@props(['valor' => 0, 'size' => 'md'])

@php
    $valor = (float) $valor;
    [$cuerpo, $anillo] = \App\Models\Ficha::paletaChip($valor);
    $texto = $valor >= 1000 ? number_format($valor / 1000, 0, ',', '.').'K' : number_format($valor, 0, ',', '.');
    $tam = ['sm' => 'h-8 w-8', 'md' => 'h-11 w-11', 'lg' => 'h-16 w-16'][$size] ?? 'h-11 w-11';
    $fuente = strlen($texto) > 3 ? 19 : 24;
@endphp

<svg viewBox="0 0 100 100" role="img" aria-label="Ficha de {{ number_format($valor, 0, ',', '.') }} pesos"
     {{ $attributes->merge(['class' => $tam.' shrink-0 drop-shadow']) }}>
    <circle cx="50" cy="50" r="48" fill="{{ $cuerpo }}" />
    <circle cx="50" cy="50" r="40" fill="none" stroke="{{ $anillo }}" stroke-width="7" stroke-dasharray="15 11" />
    <circle cx="50" cy="50" r="29" fill="{{ $anillo }}" />
    <text x="50" y="{{ 50 + $fuente * 0.36 }}" text-anchor="middle" font-family="Arial, sans-serif"
          font-size="{{ $fuente }}" font-weight="700" fill="{{ $cuerpo }}">{{ $texto }}</text>
</svg>