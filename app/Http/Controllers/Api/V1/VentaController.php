<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModeloResource;
use App\Models\Venta;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->can('ver-ventas'), 403, 'Tu rol activo no puede ver ventas.');

        return ModeloResource::collection(Venta::query()->latest('id')->paginate(15));
    }

    public function show(Request $request, Venta $venta)
    {
        abort_unless($request->user()->can('ver-ventas'), 403, 'Tu rol activo no puede ver ventas.');

        return new ModeloResource($venta);
    }
}
