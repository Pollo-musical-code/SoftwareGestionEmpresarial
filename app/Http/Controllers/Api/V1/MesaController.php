<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModeloResource;
use App\Models\Mesa;
use Illuminate\Http\Request;

class MesaController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->can('ver-mesas'), 403, 'Tu rol activo no puede ver mesas.');

        return ModeloResource::collection(Mesa::with('juego')->orderBy('id')->paginate(15));
    }
}
