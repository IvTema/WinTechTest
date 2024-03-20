<?php

namespace App\Services;

use App\Models\Rate;

/**
 * Class ConvertService.
 */
class ConvertService
{
    protected $rate;

    public function __construct(Rate $rate)
    {
        $this->rate = $rate;
    }

    public function convertCurrency($transactionData, $validated)
    {
        $currentRate = $this->rate->getCurrentRate();

        if($transactionData['currency'] != 'usd'){
            $currentRateValue = $currentRate->{$validated['currency']."_rate"};  // take currency column from rate table 
            
            $convertedAmount = intval($validated['amount']) / $currentRateValue;

            if ($convertedAmount < 1) {
                throw new \Exception('The converted amount is less than the minimum allowed value.');
            }

            return $convertedAmount;
        } else {
            return $validated['amount'];
        }
    }
}
