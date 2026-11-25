<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActiveRoleController extends Controller
{
    public function elegir(Request $request)
    {
        $user = $request->user();
        $roles = $user->roles()->pluck('name');

        if ($roles->count() <= 1) {
            return redirect()->route('dashboard');
        }

        return view('rol-activo.elegir', ['roles' => $roles, 'actual' => $user->activeRole()]);
    }

    public function guardar(Request $request)
    {
        $user = $request->user();
        $data = $request->validate(['rol' => ['required', 'string']]);

        if (! $user->roles->contains('name', $data['rol'])) {
            return back()->withErrors(['rol' => 'No tienes ese rol asignado.']);
        }

        $request->session()->put('active_role', $data['rol']);
        activity('seguridad')->causedBy($user)->withProperties(['rol_activo' => $data['rol']])->log('Eligió su rol activo');

        return redirect()->route('dashboard');
    }
}
