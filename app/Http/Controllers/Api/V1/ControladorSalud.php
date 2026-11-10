<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/health: indica si la API está funcionando.
 */
class ControladorSalud extends Controller
{
    /**
     * Estado del servicio. Un controlador con un solo método usa __invoke().
     *
     * @unauthenticated
     */
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'app' => config('app.name'),
            'version' => '1.0.0',
        ]);
    }
}
