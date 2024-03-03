<?php

namespace App\Helpers;

class ValidationUpdateRuleHelper implements ValidationRuleInterface
{
    public function getRules(): array
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