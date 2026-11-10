<?php

namespace App\Providers;

use App\Models\Contacto;
use App\Policies\PoliticaContacto;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Los permisos de Contacto están en PoliticaContacto.
        Gate::policy(Contacto::class, PoliticaContacto::class);

        // Máximo 5 intentos de inicio de sesión por minuto para cada correo + IP.
        RateLimiter::for('inicio-sesion', function (Request $solicitud) {
            return Limit::perMinute(5)
                ->by(Str::lower((string) $solicitud->input('email')).'|'.$solicitud->ip())
                ->response(fn (Request $solicitud, array $cabeceras) => response()->json([
                    'message' => 'Demasiados intentos de inicio de sesión. Espera un minuto.',
                ], 429, $cabeceras));
        });
    }
}
