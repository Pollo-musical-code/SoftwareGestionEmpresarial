<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    @php $color = $branding->primary_color ?: '#9f1239'; @endphp
    <style>
        @page { margin: 130px 40px 70px 40px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #222; }
        header { position: fixed; top: -105px; left: 0; right: 0; height: 90px; }
        footer { position: fixed; bottom: -50px; left: 0; right: 0; height: 30px;
                 font-size: 9px; color: #777; text-align: center; border-top: 1px solid #ccc; padding-top: 5px; }
        .company { font-size: 16px; font-weight: bold; color: {{ $color }}; }
        .muted { color: #666; }
        .doc-box { border: 2px solid {{ $color }}; padding: 6px 10px; text-align: center; }
        .doc-box .type { font-size: 10px; text-transform: uppercase; color: {{ $color }}; }
        .doc-box .number { font-size: 16px; font-weight: bold; }
        .initials { width: 55px; height: 55px; border-radius: 30px; background: {{ $color }}; color: #d4af37;
                    text-align: center; line-height: 55px; font-size: 20px; font-weight: bold; }
        .gold-line { border-top: 2px solid #d4af37; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; }
        .info td { padding: 3px 4px; vertical-align: top; }
        .items { margin-top: 15px; }
        .items th { background: {{ $color }}; color: #fff; padding: 6px; font-size: 10px; text-align: left; }
        .items td { padding: 5px 6px; border-bottom: 1px solid #ddd; }
        .right { text-align: right !important; }
        .totals { width: 40%; margin-left: 60%; margin-top: 10px; }
        .totals td { padding: 4px 6px; }
        .totals .grand td { font-size: 13px; font-weight: bold; border-top: 2px solid {{ $color }}; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 8px; background: #eee; font-size: 10px; }
        .signatures { margin-top: 60px; }
        .signatures td { width: 50%; text-align: center; padding-top: 35px; }
        .signatures span { border-top: 1px solid #333; padding: 4px 30px 0 30px; }
    </style>
</head>
<body>
    <header>
        <table>
            <tr>
                <td style="width: 12%;">
                    @if ($branding->logoDataUri())
                        <img src="{{ $branding->logoDataUri() }}" style="height: 55px;" alt="Logo">
                    @else
                        <div class="initials">{{ $branding->initials() }}</div>
                    @endif
                </td>
                <td style="width: 53%;">
                    <div class="company">{{ $branding->legal_name }}</div>
                    <div class="muted">NIT {{ $branding->nit }}@if ($branding->city) · {{ $branding->city }}@endif</div>
                    <div class="muted">
                        @if ($branding->address){{ $branding->address }} · @endif
                        @if ($branding->phone)Tel. {{ $branding->phone }} · @endif
                        {{ $branding->email }}
                    </div>
                </td>
                <td style="width: 35%;">
                    <div class="doc-box">
                        <div class="type">@yield('doc_type')</div>
                        <div class="number">@yield('doc_number')</div>
                    </div>
                </td>
            </tr>
        </table>
        <div class="gold-line"></div>
    </header>

    <footer>
        {{ $branding->trade_name }} · Documento generado por el ERP el {{ now()->format('d/m/Y H:i') }}<br>
        +18 · Juega con responsabilidad.
    </footer>

    <main>
        @yield('content')
    </main>
</body>
</html>