<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AccessControlService;
use App\Support\PermissionCatalog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller implements HasMiddleware
{
    public function __construct(private AccessControlService $acceso) {}

    public static function middleware(): array
    {
        return [new Middleware('permission:gestionar-usuarios')];
    }

    public function index(Request $request)
    {
        $estado = $request->query('estado', 'todos');
        $usuarios = User::with('roles')
            ->when($estado === 'activos', fn ($q) => $q->where('active', true))
            ->when($estado === 'inactivos', fn ($q) => $q->where('active', false))
            ->when($request->query('q'), fn ($q, $t) => $q->where(fn ($w) => $w->where('name', 'like', "%$t%")->orWhere('email', 'like', "%$t%")))
            ->orderBy('name')->paginate(15)->withQueryString();

        return view('configuracion.usuarios.index', compact('usuarios', 'estado'));
    }

    public function create()
    {
        return view('configuracion.usuarios.form', $this->datosFormulario(null));
    }

    public function store(Request $request)
    {
        $this->acceso->guardarUsuario($request->user(), null, $this->validar($request, null));

        return redirect()->route('settings.users.index')->with('status', 'Usuario creado.');
    }

    public function edit(User $user)
    {
        return view('configuracion.usuarios.form', $this->datosFormulario($user));
    }

    public function update(Request $request, User $user)
    {
        $this->acceso->guardarUsuario($request->user(), $user, $this->validar($request, $user));

        return redirect()->route('settings.users.index')->with('status', 'Usuario actualizado.');
    }

    public function toggle(Request $request, User $user)
    {
        $user = $this->acceso->alternarActivo($request->user(), $user);

        return back()->with('status', $user->active ? 'Usuario activado.' : 'Usuario desactivado.');
    }

    private function validar(Request $request, ?User $user): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,name'],
            'permisos' => ['nullable', 'array'],
            'permisos.*' => ['exists:permissions,name'],
        ], ['roles.required' => 'Cada usuario debe tener al menos un rol.']);

        $data['active'] = $request->boolean('active');

        return $data;
    }

    private function datosFormulario(?User $user): array
    {
        return [
            'usuario' => $user,
            'roles' => Role::orderBy('name')->get(),
            'permisos' => PermissionCatalog::grouped(Permission::orderBy('name')->get()),
        ];
    }
}
