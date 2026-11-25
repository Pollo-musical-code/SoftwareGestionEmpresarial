<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Services\AccessControlService;
use App\Support\PermissionCatalog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller implements HasMiddleware
{
    public function __construct(private AccessControlService $acceso) {}

    public static function middleware(): array
    {
        return [new Middleware('permission:gestionar-roles')];
    }

    public function index()
    {
        return view('configuracion.roles.index', [
            'roles' => Role::withCount(['users', 'permissions'])->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('configuracion.roles.form', $this->datos(null));
    }

    public function store(Request $request)
    {
        $this->acceso->guardarRol($request->user(), null, ...$this->validar($request, null));

        return redirect()->route('settings.roles.index')->with('status', 'Rol creado.');
    }

    public function edit(Role $role)
    {
        return view('configuracion.roles.form', $this->datos($role));
    }

    public function update(Request $request, Role $role)
    {
        $this->acceso->guardarRol($request->user(), $role, ...$this->validar($request, $role));

        return redirect()->route('settings.roles.index')->with('status', 'Rol actualizado.');
    }

    public function destroy(Request $request, Role $role)
    {
        $this->acceso->eliminarRol($request->user(), $role);

        return redirect()->route('settings.roles.index')->with('status', 'Rol eliminado.');
    }

    private function validar(Request $request, ?Role $role): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9\-]+$/', Rule::unique('roles', 'name')->ignore($role?->id)],
            'permisos' => ['nullable', 'array'],
            'permisos.*' => ['exists:permissions,name'],
        ], ['name.regex' => 'Usa minúsculas, números y guiones (ej. jefe-de-sala).']);

        return ['nombre' => $data['name'], 'permisos' => $data['permisos'] ?? []];
    }

    private function datos(?Role $role): array
    {
        return [
            'rol' => $role,
            'permisos' => PermissionCatalog::grouped(Permission::orderBy('name')->get()),
        ];
    }
}
