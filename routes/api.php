<?php

use App\Http\Controllers\Api\V1\ControladorAutenticacion;
use App\Http\Controllers\Api\V1\ControladorContactos;
use App\Http\Controllers\Api\V1\ControladorSalud;
use Illuminate\Support\Facades\Route;

// Todas las rutas quedan bajo /api/v1/...
Route::prefix('v1')->group(function () {
    Route::get('/health', ControladorSalud::class)->name('salud');

    // Rutas públicas, con límite de 5 intentos por minuto.
    Route::post('/auth/register', [ControladorAutenticacion::class, 'registrar'])
        ->middleware('throttle:inicio-sesion')
        ->name('auth.registrar');

    Route::post('/auth/login', [ControladorAutenticacion::class, 'iniciarSesion'])
        ->middleware('throttle:inicio-sesion')
        ->name('auth.iniciar_sesion');

    // Rutas que exigen token (sesión iniciada). Sin token responden 401.
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [ControladorAutenticacion::class, 'perfil'])->name('auth.perfil');
        Route::put('/auth/me', [ControladorAutenticacion::class, 'actualizarPerfil'])->name('auth.actualizar_perfil');
        Route::post('/auth/logout', [ControladorAutenticacion::class, 'cerrarSesion'])->name('auth.cerrar_sesion');

        // CRUD de contactos. {contacto} es el id que llega en la URL.
        Route::get('/contacts', [ControladorContactos::class, 'listar'])->name('contactos.listar');
        Route::post('/contacts', [ControladorContactos::class, 'crear'])->name('contactos.crear');
        Route::get('/contacts/{contacto}', [ControladorContactos::class, 'ver'])->name('contactos.ver');
        Route::match(['put', 'patch'], '/contacts/{contacto}', [ControladorContactos::class, 'actualizar'])->name('contactos.actualizar');
        Route::delete('/contacts/{contacto}', [ControladorContactos::class, 'eliminar'])->name('contactos.eliminar');
    });
});
