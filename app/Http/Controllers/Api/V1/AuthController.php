<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /** POST /api/v1/login  {email, password, rol?}  -> token con habilidad "rol:<nombre>" (rol activo). */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'rol' => ['nullable', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas.'], 401);
        }
        if (! (bool) $user->active) {
            return response()->json(['message' => 'Usuario inactivo.'], 403);
        }

        $roles = $user->roles->pluck('name');
        if ($roles->isEmpty()) {
            return response()->json(['message' => 'El usuario no tiene roles asignados.'], 403);
        }

        $rol = $data['rol'] ?? ($roles->count() === 1 ? $roles->first() : null);
        if ($rol === null) {
            return response()->json([
                'message' => 'Tienes varios roles: envía el campo "rol" con el rol activo.',
                'roles' => $roles->values(),
            ], 422);
        }
        if (! $roles->contains($rol)) {
            return response()->json(['message' => 'No tienes ese rol.'], 403);
        }

        $token = $user->createToken('api-casino', ["rol:$rol"])->plainTextToken;

        return response()->json(['token' => $token, 'rol_activo' => $rol, 'tipo' => 'Bearer']);
    }

    public function me(Request $request)
    {
        $u = $request->user();
        $rol = $u->roles->firstWhere('name', $u->activeRole());
        $permisos = collect($rol ? $rol->permissions->pluck('name') : [])
            ->merge($u->permissions->pluck('name'))->unique()->values();

        return response()->json([
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'roles' => $u->roles->pluck('name'),
            'rol_activo' => $u->activeRole(),
            'permisos' => $permisos,
        ]);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();
        if ($token && method_exists($token, 'delete')) {
            $token->delete();
        }

        return response()->json(['message' => 'Sesión cerrada.']);
    }
}
