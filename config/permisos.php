<?php

// Etiquetas en español para la matriz de permisos del casino.
// Los permisos se nombran "accion-modulo" (ej. ver-clientes, recibir-compras).
return [
    'modulos' => [
        'juegos' => 'Juegos',
        'mesas' => 'Mesas',
        'clientes' => 'Clientes (jugadores)',
        'fichas' => 'Fichas',
        'ventas' => 'Ventas de fichas',
        'proveedores' => 'Proveedores',
        'compras' => 'Compras',
        'reportes' => 'Reportes',
        'inventario' => 'Inventario / Kardex',
        'auditoria' => 'Auditoría',
        'branding' => 'Identidad visual',
        'usuarios' => 'Usuarios',
        'roles' => 'Roles y permisos',
    ],
    'acciones' => [
        'ver' => 'Ver',
        'crear' => 'Crear',
        'editar' => 'Editar',
        'eliminar' => 'Eliminar / anular',
        'recibir' => 'Recibir',
        'ajustar' => 'Ajustar',
        'exportar' => 'Exportar Excel',
        'importar' => 'Importar Excel',
        'gestionar' => 'Gestionar',
    ],
    // Roles base que no se pueden eliminar.
    'roles_base' => ['admin', 'cajero', 'crupier', 'supervisor'],
];
