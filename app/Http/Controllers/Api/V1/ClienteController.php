<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClienteResource;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->can('ver-clientes'), 403, 'Tu rol activo no puede ver clientes.');

        $clientes = Cliente::query()
            ->when($request->query('q'), fn ($q, $t) => $q->where(fn ($w) => $w->where('nombre', 'like', "%$t%")->orWhere('documento', 'like', "%$t%")))
            ->orderBy('nombre')->paginate(15);

        return ClienteResource::collection($clientes);
    }

    public function show(Request $request, Cliente $cliente)
    {
        abort_unless($request->user()->can('ver-clientes'), 403, 'Tu rol activo no puede ver clientes.');

        return new ClienteResource($cliente);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->can('crear-clientes'), 403, 'Tu rol activo no puede crear clientes.');

        $data = $request->validate([
            'documento' => ['required', 'string', 'max:30', Rule::unique('clientes', 'documento')->whereNull('deleted_at')],
            'nombre' => ['required', 'string', 'max:150'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:-18 years'],
            'nivel_vip' => ['required', 'string', 'max:30'],
        ], ['fecha_nacimiento.before_or_equal' => 'El cliente debe ser mayor de 18 años.']);

        $cliente = new Cliente;
        $cliente->forceFill($data + ['saldo_fichas' => 0])->save();

        return (new ClienteResource($cliente))->response()->setStatusCode(201);
    }
}
