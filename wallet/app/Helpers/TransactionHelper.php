<?php

namespace App\Helpers;

use App\Http\Resources\BalanceStatusResource;
use Illuminate\Http\JsonResponse;

class TransactionHelper
{
    public static function createTransactionResponse($transaction, $balance): JsonResponse
    {
        return response()->json([
            'message' => 'Transaction created successfully',
            'transaction' => [
                'transaction_type' => $transaction->transaction_type,
                'amount' => $transaction->amount,
                'currency' => $transaction->currency,
                'issue' => $transaction->issue,
                'balance' => new BalanceStatusResource($balance),
            ],
        ]);
    }

    public static function transformToTransactionArray(array $validated): array
    {
        return [
            'balance_id' => $validated['amount'],
            'amount' => $validated['amount'],
            'transaction_type' => $validated['transaction'],
            'currency' => $validated['currency'],
            'issue' => $validated['issue'],
        ];
    }
}