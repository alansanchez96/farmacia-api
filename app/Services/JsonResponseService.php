<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonResponseService
{
    public function jsonSuccess(string $action): JsonResponse
    {
        return response()->json([
            'msg' => "Se ha {$action} satisfactoriamente."
        ], Response::HTTP_OK);
    }

    public function jsonFailure(string $error): JsonResponse
    {
        return response()->json([
            'error' => $error
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
