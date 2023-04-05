<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonResponseService
{
    public function jsonSuccess(string $action, string $key = null, mixed $value = null): JsonResponse
    {
        return response()->json([
            $key => $value,
            'msg' => "Se ha {$action} satisfactoriamente."
        ], Response::HTTP_OK);
    }

    public function jsonFailure(): JsonResponse
    {
        return response()->json([
            'error' => 'Ha ocurrido un problema.'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
