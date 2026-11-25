<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        // Usuario inactivo: se le cierra la sesión.
        if (! (bool) ($user->active ?? true)) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Tu usuario está inactivo. Contacta al administrador del casino.']);
        }

        if ($request->routeIs('rol-activo.*', 'logout')) {
            return $next($request);
        }

        // Varios roles y ninguno elegido: debe escoger el rol activo.
        if ($user->hasMultipleRoles() && $user->activeRole() === null) {
            return redirect()->route('rol-activo.elegir');
        }

        return $next($request);
    }
}
