<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class JsonResponseService
{
    /**
     * Retorna un mensaje de accion personalizada 
     * De forma opcional puede agregarse una coleccion, objeto, etc como value
     *
     * @param string $action
     * @param mixed|null $value
     * @return JsonResponse
     */
    public function jsonSuccess(string $action, mixed $value = null): JsonResponse
    {
        $data = [
            'message' => "Se ha {$action} satisfactoriamente.",
            ($value === null) ?: 'data' => $value,
        ];

        return response()
            ->json(array_filter($data), Response::HTTP_OK);
    }

    /**
     * Retorna un mensaje de error
     * 
     * @return JsonResponse
     */
    public function jsonFailure(string $message): JsonResponse
    {
        Log::error('error: ' . $message);

        return response()
            ->json([
                'error' => 'Ha ocurrido un error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
