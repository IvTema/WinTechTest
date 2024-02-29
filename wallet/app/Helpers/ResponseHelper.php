<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    public static function checkOrDropError($element)
    {
        if ($element === null || $element->count() === 0) {
            return response()->json([
                'error' => 'Not found',
                'message' => 'Data not provided',
            ], 404);
        }

        return null;  // No validation errors
    }

    public static function InsufficientBalanceError(): JsonResponse
    {
        return response()->json([
            'error' => 'Bad Request',
            'message' => 'Insufficient balance to perform the debit transaction.',
        ], 400);
    }

    public static function ServerBdError(): JsonResponse
    {
        return response()->json([
            'error' => 'Internal Server Error',
            'message' => 'Failed to update balance. Please try again later.',
        ], 500);
    }
}