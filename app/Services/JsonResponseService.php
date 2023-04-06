<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonResponseService
{
    /**
     * Retorna un mensaje de accion personalizada 
     * de forma opcional puede agregarse una coleccion, objeto, etc
     *
     * @param string $action
     * @param string|null $key
     * @param mixed $value
     * @return JsonResponse
     */
    public function jsonSuccess(string $action, string $key = null, mixed $value = null): JsonResponse
    {
        return response()->json([
            $key => $value,
            'msg' => "Se ha {$action} satisfactoriamente."
        ], Response::HTTP_OK);
    }

    /**
     * Retorna un mensaje de accion fallida
     *
     * @return JsonResponse
     */
    public function jsonFailure(): JsonResponse
    {
        return response()->json([
            'error' => 'Ha ocurrido un problema.'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
