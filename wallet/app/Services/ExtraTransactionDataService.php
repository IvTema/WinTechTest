<?php

namespace App\Services;

/**
 * Class ExtraTransactionDataService.
 */
class ExtraTransactionDataService
{
    public function addBalanceAndRate($transactionData, $balance, $rate)
    {
        $transactionData['rate'] = $rate;
        $transactionData['balance'] = $balance->usd;

        return $transactionData;
    }
}
