<?php

namespace App\Services;

use App\Models\Balance;

/**
 * Class OperationService.
 */
class OperationService
{
    public function executeOperation($validated, $balance, $convertedAmount) : Balance
    {
        if($validated['transaction'] == 'debit'){
            $balance->usd = $balance->usd + $convertedAmount;
        } elseif ($validated['transaction'] == 'credit' && ($balance->usd - $convertedAmount) > 0){
            $balance->usd = $balance->usd - $convertedAmount;
        } else {
            throw new \Exception('Insufficient balance to perform the debit transaction.');
        }

        return $balance;
    }
}
