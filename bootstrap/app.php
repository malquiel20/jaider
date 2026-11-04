<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

        // Sin sesión iniciada: 401 y se indica a dónde ir para iniciar sesión.
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'No has iniciado sesión. Inicia sesión para continuar.',
                    'login_url' => route('auth.login'),
                ], 401);
            }
        });

        // Link que no existe: ruta inexistente o registro inexistente (o eliminado).
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if (! ($request->is('api/*') || $request->expectsJson())) {
                return null;
            }

            $message = $e->getPrevious() instanceof ModelNotFoundException
                ? 'El recurso solicitado no existe.'
                : 'La ruta solicitada no existe.';

            return response()->json([
                'message' => $message,
                'path' => '/'.$request->path(),
            ], 404);
        });
    })->create();
