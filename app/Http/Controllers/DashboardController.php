<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Compra;
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
        $peso = fn ($n) => '$'.number_format((float) $n, 0, ',', '.');

        // Accesos rápidos: cada módulo solo aparece si el usuario tiene el permiso
        $modules = collect([
            ['title' => 'Mesas de juego', 'description' => 'Qué mesas están abiertas y con qué juego.', 'route' => 'mesas.index', 'permission' => 'ver-mesas', 'icon' => 'table'],
            ['title' => 'Juegos', 'description' => 'Ruleta, blackjack y demás, con sus límites de apuesta.', 'route' => 'juegos.index', 'permission' => 'ver-juegos', 'icon' => 'dice'],
            ['title' => 'Clientes', 'description' => 'Jugadores registrados y su nivel VIP.', 'route' => 'clientes.index', 'permission' => 'ver-clientes', 'icon' => 'users'],
            ['title' => 'Venta de fichas', 'description' => 'Vende fichas en caja y genera la factura.', 'route' => 'ventas.index', 'permission' => 'ver-ventas', 'icon' => 'cart'],
            ['title' => 'Fichas en caja', 'description' => 'Cuántas fichas quedan de cada denominación.', 'route' => 'fichas.index', 'permission' => 'ver-fichas', 'icon' => 'chip'],
            ['title' => 'Compras de fichas', 'description' => 'Pide fichas a los proveedores y recíbelas.', 'route' => 'compras.index', 'permission' => 'ver-compras', 'icon' => 'truck'],
        ])->filter(fn ($m) => $user->can($m['permission']))->values();

        $stats = collect();
        $charts = collect();

        // --- Sala de juegos ---
        if ($user->can('ver-mesas')) {
            $totalMesas = Mesa::count();
            $stats->push([
                'label' => 'Mesas abiertas',
                'value' => Mesa::where('estado', 'abierta')->count(),
                'hint' => "de {$totalMesas} mesa(s) en total",
                'icon' => 'table',
                'tone' => 'green',
            ]);

            $abiertas = Mesa::where('estado', 'abierta')->count();
            $cerradas = Mesa::where('estado', 'cerrada')->count();
            if ($abiertas + $cerradas > 0) {
                $charts->push([
                    'id' => 'mesas-estado',
                    'title' => 'Estado de las mesas',
                    'subtitle' => 'Cuántas mesas están jugando ahora y cuántas están cerradas.',
                    'type' => 'doughnut',
                    'labels' => ['Abiertas', 'Cerradas'],
                    'colors' => ['#15803d', '#94a3b8'],
                    'datasets' => [['data' => [$abiertas, $cerradas]]],
                ]);
            }
        }

        // --- Clientes ---
        if ($user->can('ver-clientes')) {
            $stats->push(['label' => 'Clientes registrados', 'value' => Cliente::count(), 'hint' => 'jugadores en la base de datos', 'icon' => 'users', 'tone' => 'blue']);
            $stats->push([
                'label' => 'Saldo de fichas de clientes',
                'value' => $peso(Cliente::sum('saldo_fichas')),
                'hint' => 'lo que los clientes tienen en fichas',
                'icon' => 'banknotes',
                'tone' => 'gold',
            ]);
        }

        // --- Fichas (inventario de caja) ---
        if ($user->can('ver-fichas')) {
            $stats->push([
                'label' => 'Fichas con stock bajo',
                'value' => Ficha::where('activo', true)->where('stock', '<=', 5)->count(),
                'hint' => 'denominaciones con 5 unidades o menos',
                'icon' => 'alert',
                'tone' => 'red',
            ]);
            $stats->push([
                'label' => 'Valor de fichas en caja',
                'value' => $peso(Ficha::where('activo', true)->sum(DB::raw('valor * stock'))),
                'hint' => 'suma de valor × unidades disponibles',
                'icon' => 'chip',
                'tone' => 'brand',
            ]);

            $fichas = Ficha::where('activo', true)->orderBy('valor')->get(['denominacion', 'valor', 'stock']);
            if ($fichas->isNotEmpty()) {
                $charts->push([
                    'id' => 'stock-fichas',
                    'title' => 'Fichas disponibles en caja',
                    'subtitle' => 'Unidades que quedan de cada ficha. Con el color de la ficha real.',
                    'type' => 'bar',
                    'labels' => $fichas->pluck('denominacion')->all(),
                    'colors' => $fichas->map(fn ($f) => Ficha::paletaChip((float) $f->valor)[0])->all(),
                    'datasets' => [['label' => 'Unidades', 'data' => $fichas->pluck('stock')->all()]],
                ]);
            }
        }

        // --- Ventas ---
        if ($user->can('ver-ventas')) {
            $ventasMes = Venta::whereMonth('fecha_venta', now()->month)->whereYear('fecha_venta', now()->year);
            $stats->push([
                'label' => 'Vendido en el mes',
                'value' => $peso((clone $ventasMes)->sum('total')),
                'hint' => (clone $ventasMes)->count().' factura(s) este mes',
                'icon' => 'cart',
                'tone' => 'brand',
            ]);

            // Ventas de los últimos 14 días
            $desde = now()->subDays(13)->startOfDay();
            $porDia = Venta::whereDate('fecha_venta', '>=', $desde->toDateString())
                ->selectRaw('DATE(fecha_venta) as dia, SUM(total) as total')
                ->groupBy('dia')
                ->pluck('total', 'dia');
            $dias = collect(range(0, 13))->map(fn ($i) => $desde->copy()->addDays($i));

            $charts->push([
                'id' => 'ventas-dia',
                'title' => 'Ventas de los últimos 14 días',
                'subtitle' => 'Cuánto se vendió en fichas cada día (sin contar ventas anuladas).',
                'type' => 'line',
                'money' => true,
                'wide' => true,
                'labels' => $dias->map(fn ($d) => $d->format('d/m'))->all(),
                'datasets' => [['label' => 'Ventas', 'data' => $dias->map(fn ($d) => (float) ($porDia[$d->toDateString()] ?? 0))->all()]],
            ]);

            // Fichas más vendidas en los últimos 30 días
            $porFicha = VentaDetalle::query()
                ->join('ventas', 'ventas.id', '=', 'venta_detalles.venta_id')
                ->join('fichas', 'fichas.id', '=', 'venta_detalles.ficha_id')
                ->whereNull('ventas.deleted_at')
                ->whereDate('ventas.fecha_venta', '>=', now()->subDays(29)->toDateString())
                ->select('fichas.denominacion', 'fichas.valor', DB::raw('SUM(venta_detalles.cantidad) as cantidad'))
                ->groupBy('fichas.id', 'fichas.denominacion', 'fichas.valor')
                ->orderBy('fichas.valor')
                ->get();

            if ($porFicha->isNotEmpty()) {
                $charts->push([
                    'id' => 'ventas-ficha',
                    'title' => 'Fichas más vendidas (30 días)',
                    'subtitle' => 'Qué denominaciones piden más los clientes.',
                    'type' => 'doughnut',
                    'labels' => $porFicha->pluck('denominacion')->all(),
                    'colors' => $porFicha->map(fn ($f) => Ficha::paletaChip((float) $f->valor)[0])->all(),
                    'datasets' => [['data' => $porFicha->pluck('cantidad')->map(fn ($c) => (int) $c)->all()]],
                ]);
            }

            // Los 5 clientes que más han comprado
            $topClientes = Venta::query()
                ->join('clientes', 'clientes.id', '=', 'ventas.cliente_id')
                ->select('clientes.nombre', DB::raw('SUM(ventas.total) as total'))
                ->groupBy('clientes.id', 'clientes.nombre')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            if ($topClientes->isNotEmpty()) {
                $charts->push([
                    'id' => 'top-clientes',
                    'title' => 'Top 5 clientes',
                    'subtitle' => 'Los jugadores que más han comprado en fichas.',
                    'type' => 'bar',
                    'money' => true,
                    'horizontal' => true,
                    'labels' => $topClientes->pluck('nombre')->all(),
                    'datasets' => [['label' => 'Comprado', 'data' => $topClientes->pluck('total')->map(fn ($t) => (float) $t)->all()]],
                ]);
            }
        }

        // --- Compras (abastecimiento) ---
        if ($user->can('ver-compras')) {
            $porRecibir = Compra::whereIn('estado', ['pendiente', 'parcial']);
            $stats->push([
                'label' => 'Órdenes por recibir',
                'value' => (clone $porRecibir)->count(),
                'hint' => $peso((clone $porRecibir)->sum('total')).' pendientes de llegar',
                'icon' => 'clock',
                'tone' => 'violet',
            ]);
            $stats->push([
                'label' => 'Comprado en el mes',
                'value' => $peso(Compra::whereNot('estado', 'anulada')->whereMonth('fecha_compra', now()->month)->whereYear('fecha_compra', now()->year)->sum('total')),
                'hint' => 'gasto en fichas a proveedores',
                'icon' => 'truck',
                'tone' => 'slate',
            ]);
        }

        // Tabla accionable: fichas para reabastecer
        $fichasReabastecer = $user->can('ver-fichas')
            ? Ficha::where('activo', true)->where('stock', '<=', 5)->orderBy('stock')->get()
            : collect();

        return view('dashboard', [
            'user' => $user,
            'roles' => $user->getRoleNames(),
            'modules' => $modules,
            'stats' => $stats,
            'charts' => $charts,
            'fichasReabastecer' => $fichasReabastecer,
            'totalMesas' => $user->can('ver-mesas') ? Mesa::count() : null,
        ]);
    }
}