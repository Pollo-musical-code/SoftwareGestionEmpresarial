<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Ficha;
use App\Models\Mesa;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        // Cada módulo solo aparece si el usuario tiene el permiso
        $modules = collect([
            ['title' => 'Mesas', 'route' => 'mesas.index', 'permission' => 'ver-mesas'],
            ['title' => 'Juegos', 'route' => 'juegos.index', 'permission' => 'ver-juegos'],
            ['title' => 'Clientes', 'route' => 'clientes.index', 'permission' => 'ver-clientes'],
            ['title' => 'Fichas', 'route' => 'fichas.index', 'permission' => 'ver-fichas'],
            ['title' => 'Ventas', 'route' => 'ventas.index', 'permission' => 'ver-ventas'],
        ])->filter(fn ($m) => $user->can($m['permission']))->values();

        $stats = collect();

        if ($user->can('ver-mesas')) {
            $stats->push(['label' => 'Mesas abiertas', 'value' => Mesa::where('estado', 'abierta')->count()]);
        }

        if ($user->can('ver-clientes')) {
            $stats->push(['label' => 'Clientes registrados', 'value' => Cliente::count()]);
            $stats->push(['label' => 'Saldo de fichas de clientes', 'value' => '$ '.number_format((float) Cliente::sum('saldo_fichas'), 0, ',', '.')]);
        }

        if ($user->can('ver-fichas')) {
            $stats->push(['label' => 'Fichas con stock bajo (≤ 5)', 'value' => Ficha::where('activo', true)->where('stock', '<=', 5)->count()]);
            $stats->push(['label' => 'Valor de fichas en caja', 'value' => '$ '.number_format((float) Ficha::where('activo', true)->sum(DB::raw('valor * stock')), 0, ',', '.')]);
        }

        $topFichas = collect();
        $topClientes = collect();

        if ($user->can('ver-ventas')) {
            $stats->push([
                'label' => 'Ventas del mes',
                'value' => Venta::whereMonth('fecha_venta', now()->month)->whereYear('fecha_venta', now()->year)->count(),
            ]);
            $stats->push([
                'label' => 'Total vendido del mes',
                'value' => '$ '.number_format((float) Venta::whereMonth('fecha_venta', now()->month)->whereYear('fecha_venta', now()->year)->sum('total'), 0, ',', '.'),
            ]);

            // Top 5 fichas más vendidas (sin contar ventas anuladas)
            $topFichas = VentaDetalle::query()
                ->join('ventas', 'ventas.id', '=', 'venta_detalles.venta_id')
                ->join('fichas', 'fichas.id', '=', 'venta_detalles.ficha_id')
                ->whereNull('ventas.deleted_at')
                ->select('fichas.denominacion', DB::raw('SUM(venta_detalles.cantidad) as cantidad'), DB::raw('SUM(venta_detalles.subtotal) as valor'))
                ->groupBy('fichas.id', 'fichas.denominacion')
                ->orderByDesc('cantidad')
                ->limit(5)
                ->get();

            // Top 5 clientes con más compras
            $topClientes = Venta::query()
                ->join('clientes', 'clientes.id', '=', 'ventas.cliente_id')
                ->select('clientes.nombre', DB::raw('COUNT(ventas.id) as compras'), DB::raw('SUM(ventas.total) as total'))
                ->groupBy('clientes.id', 'clientes.nombre')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
        }

        return view('dashboard', [
            'roles' => $user->getRoleNames(),
            'modules' => $modules,
            'stats' => $stats,
            'topFichas' => $topFichas,
            'topClientes' => $topClientes,
        ]);
    }
}