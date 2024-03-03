<?php

namespace App\Helpers;

class ValidationHelper
{
    public static function validateOrDropError($validator)
    {
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Bad Request',
                'message' => 'Invalid data provided',
            ], 400);
        }

        return null; // No validation errors
    }
}