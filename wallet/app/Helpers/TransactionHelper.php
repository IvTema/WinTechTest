<?

namespace App\Helpers;

use App\Http\Resources\BalanceStatusCollection;

class TransactionHelper
{
    public static function createTransactionResponse($transaction, $balance)
    {
        return response()->json([
            'message' => 'Transaction created successfully',
            'transaction' => [
                'transaction_type' => $transaction->transaction_type,
                'amount' => $transaction->amount,
                'currency' => $transaction->currency,
                'issue' => $transaction->issue,
                'balance' => new BalanceStatusCollection([$balance]),
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