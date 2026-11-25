<?php

// Parámetros del negocio (Casino Fortuna) configurables por .env
return [
    // Fichas en inventario: al llegar a este valor o menos se alerta a los administradores.
    'stock_minimo' => (int) env('ERP_STOCK_MINIMO', 20),
    // Una venta de fichas igual o mayor a este valor genera alerta (control antilavado).
    'venta_alta' => (float) env('ERP_VENTA_ALTA', 5000000),
    // Hora del resumen diario.
    'resumen_hora' => env('ERP_RESUMEN_HORA', '22:00'),
];
