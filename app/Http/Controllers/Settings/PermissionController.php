<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Services\AccessControlService;
use App\Support\PermissionCatalog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/** Matriz de permisos: filas = permisos, columnas = roles. */
class PermissionController extends Controller implements HasMiddleware
{
    public function __construct(private AccessControlService $acceso) {}

    public static function middleware(): array
    {
        return [new Middleware('permission:gestionar-roles')];
    }

    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();

        return view('configuracion.permisos.matriz', [
            'roles' => $roles,
            'grupos' => PermissionCatalog::grouped(Permission::orderBy('name')->get()),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'matriz' => ['nullable', 'array'],
            'matriz.*' => ['array'],
            'matriz.*.*' => ['exists:permissions,name'],
        ]);

        $this->acceso->sincronizarMatriz($request->user(), $request->input('matriz', []));

        return back()->with('status', 'Matriz de permisos guardada.');
    }
}
