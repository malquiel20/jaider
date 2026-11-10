<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Indica si la petición es de la API (empieza por api/ o pide JSON).
 */
$esPeticionApi = fn (Request $solicitud): bool => $solicitud->is('api/*') || $solicitud->expectsJson();

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // La API no tiene página de login: sin token se responde 401, no se redirige.
        $middleware->redirectGuestsTo(fn () => null);
    })
    ->withExceptions(function (Exceptions $excepciones) use ($esPeticionApi): void {
        // En la API, todos los errores se responden en JSON.
        $excepciones->shouldRenderJsonWhen(
            fn (Request $solicitud, Throwable $error) => $esPeticionApi($solicitud)
        );

        // Sin sesión iniciada: 401 y se indica a dónde ir para iniciar sesión.
        $excepciones->render(function (AuthenticationException $error, Request $solicitud) use ($esPeticionApi) {
            if (! $esPeticionApi($solicitud)) {
                return null;
            }

            return response()->json([
                'message' => 'No has iniciado sesión. Inicia sesión para continuar.',
                'login_url' => route('auth.iniciar_sesion'),
            ], 401);
        });

        // Link que no existe: ruta inexistente o registro inexistente (o eliminado).
        $excepciones->render(function (NotFoundHttpException $error, Request $solicitud) use ($esPeticionApi) {
            if (! $esPeticionApi($solicitud)) {
                return null;
            }

            $mensaje = $error->getPrevious() instanceof ModelNotFoundException
                ? 'El recurso solicitado no existe.'
                : 'La ruta solicitada no existe.';

            return response()->json([
                'message' => $mensaje,
                'path' => '/'.$solicitud->path(),
            ], 404);
        });
    })->create();
