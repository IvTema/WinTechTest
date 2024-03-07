<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    public static function createErrorResponse(string $message, int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'error' => 'Bad Request',
            'message' => $message,
        ], $statusCode);
    }
}