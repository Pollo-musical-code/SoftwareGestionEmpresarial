<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModeloResource;
use App\Models\Ficha;
use Illuminate\Http\Request;

class FichaController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->can('ver-fichas'), 403, 'Tu rol activo no puede ver fichas.');

        return ModeloResource::collection(Ficha::query()->orderBy('id')->paginate(15));
    }

    public function show(Request $request, Ficha $ficha)
    {
        abort_unless($request->user()->can('ver-fichas'), 403, 'Tu rol activo no puede ver fichas.');

        return new ModeloResource($ficha);
    }
}
