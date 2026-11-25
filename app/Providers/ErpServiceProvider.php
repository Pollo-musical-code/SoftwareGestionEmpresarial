<?php

namespace App\Providers;

use App\Http\Middleware\EnsureActiveRole;
use App\Models\Compra;
use App\Models\Ficha;
use App\Models\Venta;
use App\Observers\CompraObserver;
use App\Observers\FichaObserver;
use App\Observers\VentaObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;

class ErpServiceProvider extends ServiceProvider
{
    private array $permisosConocidos = [];

    public function boot(): void
    {
        // Permisos efectivos = rol ACTIVO + permisos directos (reemplaza el chequeo de Spatie:
        // config/permission.php -> register_permission_check_method = false).
        Gate::before(function ($user, string $ability) {
            if (! method_exists($user, 'hasPermissionInActiveRole') || ! $this->esPermiso($ability)) {
                return null;
            }

            return $user->hasPermissionInActiveRole($ability) ? true : null;
        });

        app('router')->pushMiddlewareToGroup('web', EnsureActiveRole::class);

        // Rutas de cada fase (se cargan solo si el archivo existe).
        foreach (['configuracion', 'excel', 'notificaciones'] as $archivo) {
            $ruta = base_path("routes/$archivo.php");
            if (is_file($ruta)) {
                Route::middleware(['web', 'auth'])->group($ruta);
            }
        }

        // Observers de la fase 3 (eventos).
        if (class_exists(FichaObserver::class)) {
            Ficha::observe(FichaObserver::class);
            Venta::observe(VentaObserver::class);
            Compra::observe(CompraObserver::class);
        }
    }

    private function esPermiso(string $nombre): bool
    {
        if (! array_key_exists($nombre, $this->permisosConocidos)) {
            try {
                $this->permisosConocidos[$nombre] = Permission::where('name', $nombre)->exists();
            } catch (\Throwable) {
                $this->permisosConocidos[$nombre] = false;
            }
        }

        return $this->permisosConocidos[$nombre];
    }
}
