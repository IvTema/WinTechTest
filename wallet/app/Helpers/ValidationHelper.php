<?

namespace App\Helpers;

class ValidationHelper
{
    public static function getBalanceUpdateRules(): array
    {
        return [
            'id' => 'integer|min:1',
            'amount' => 'numeric|min:1',
            'transaction' => 'string|in:debit,credit',
            'currency' => 'string|in:usd,rub',
            'issue' => 'string|in:refund,stock,renunciation',
        ];
    }
}