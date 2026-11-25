<?php

namespace App\Providers;

use App\Models\CompanySetting;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Identidad corporativa: se consulta UNA vez por petición y se reutiliza
        $this->app->scoped('branding', function () {
            try {
                return CompanySetting::query()->first() ?? new CompanySetting(CompanySetting::DEFAULTS);
            } catch (QueryException) {
                // La tabla aún no existe (antes de migrar): se usan los valores iniciales
                return new CompanySetting(CompanySetting::DEFAULTS);
            }
        });
    }

    public function boot(): void
    {
        // $branding disponible en TODAS las vistas (menú, inicio de sesión, PDF...)
        View::composer('*', function ($view) {
            $view->with('branding', CompanySetting::current());
        });
    }
}
