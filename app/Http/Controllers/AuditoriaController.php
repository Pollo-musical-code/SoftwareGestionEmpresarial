<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Compra;
use App\Models\Ficha;
use App\Models\Juego;
use App\Models\Mesa;
use App\Models\Proveedor;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Activitylog\Models\Activity;

class AuditoriaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-auditoria'),
        ];
    }

    // Bitácora: quién hizo qué, cuándo y qué cambió
    public function index(Request $request)
    {
        $actividades = Activity::query()
            ->with(['causer', 'subject' => function (MorphTo $morphTo) {
                // Los registros en la papelera (p. ej. una venta anulada) también muestran su nombre o número
                $morphTo->constrain([
                    Juego::class => fn ($q) => $q->withTrashed(),
                    Mesa::class => fn ($q) => $q->withTrashed(),
                    Cliente::class => fn ($q) => $q->withTrashed(),
                    Ficha::class => fn ($q) => $q->withTrashed(),
                    Proveedor::class => fn ($q) => $q->withTrashed(),
                    Venta::class => fn ($q) => $q->withTrashed(),
                    Compra::class => fn ($q) => $q->withTrashed(),
                ]);
            }])
            ->when($request->filled('modulo'), fn ($q) => $q->where('log_name', $request->input('modulo')))
            ->when($request->filled('evento'), fn ($q) => $q->where('event', $request->input('evento')))
            ->when($request->filled('usuario'), fn ($q) => $q->where('causer_type', (new User)->getMorphClass())
                ->where('causer_id', $request->integer('usuario')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('to')))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('auditoria.index', [
            'actividades' => $actividades,
            'modulos' => Activity::query()->distinct()->orderBy('log_name')->pluck('log_name'),
            'usuarios' => User::orderBy('name')->get(['id', 'name']),
            'eventos' => ['created' => 'Creado', 'updated' => 'Actualizado', 'deleted' => 'Eliminado', 'restored' => 'Restaurado'],
        ]);
    }
}
