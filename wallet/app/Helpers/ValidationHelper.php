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

    public static function getIndexRules(): array
    {
        return [
            'id' => 'required|integer|min:1',
        ];
    }

    public static function getUpdateRules(): array
    {
        return [
            'id' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:1',
            'transaction' => 'required|string|in:debit,credit',
            'currency' => 'required|string|in:usd,rub',
            'issue' => 'required|string|in:refund,stock,renunciation',
        ];
    }
}