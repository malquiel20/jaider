<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Autenticacion\SolicitudActualizarPerfil;
use App\Http\Requests\Autenticacion\SolicitudInicioSesion;
use App\Http\Requests\Autenticacion\SolicitudRegistro;
use App\Http\Resources\RecursoUsuario;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Registro, inicio y cierre de sesión, y perfil del usuario.
 */
class ControladorAutenticacion extends Controller
{
    /**
     * POST /auth/register: registra un usuario nuevo y le entrega un token.
     *
     * @unauthenticated
     */
    public function registrar(SolicitudRegistro $solicitud): JsonResponse
    {
        // device_name y password_confirmation no son columnas de la tabla users.
        $datos = $solicitud->safe()->except(['device_name', 'password_confirmation']);

        $usuario = Usuario::create($datos);

        $token = $usuario->createToken($solicitud->validated('device_name'))->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => new RecursoUsuario($usuario),
        ], 201);
    }

    /**
     * POST /auth/login: revisa correo y contraseña y entrega un token.
     *
     * @unauthenticated
     */
    public function iniciarSesion(SolicitudInicioSesion $solicitud): JsonResponse
    {
        $usuario = Usuario::where('email', $solicitud->validated('email'))->first();

        // Mismo mensaje si el correo no existe o si la contraseña está mal,
        // para no revelar qué correos están registrados.
        if (! $usuario || ! Hash::check($solicitud->validated('password'), $usuario->password)) {
            throw ValidationException::withMessages([
                'email' => 'Las credenciales no son correctas.',
            ]);
        }

        $token = $usuario->createToken($solicitud->validated('device_name'))->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => new RecursoUsuario($usuario),
        ]);
    }

    /**
     * GET /auth/me: perfil del usuario que inició sesión.
     */
    public function perfil(Request $solicitud): RecursoUsuario
    {
        $usuario = $solicitud->user()->loadCount('contactos');

        return new RecursoUsuario($usuario);
    }

    /**
     * PUT /auth/me: actualiza el perfil del usuario que inició sesión.
     */
    public function actualizarPerfil(SolicitudActualizarPerfil $solicitud): RecursoUsuario
    {
        $usuario = $solicitud->user();

        $usuario->update($solicitud->safe()->except(['password_confirmation']));

        return new RecursoUsuario($usuario->loadCount('contactos'));
    }

    /**
     * POST /auth/logout: cierra la sesión borrando el token que se usó.
     */
    public function cerrarSesion(Request $solicitud): Response
    {
        $solicitud->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
