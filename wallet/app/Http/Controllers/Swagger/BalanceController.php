<?php

// 30 минута

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ResponseHelper;
use App\Helpers\TransactionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Balance\ShowRequest;
use App\Http\Requests\Balance\UpdateRequest;
use App\Http\Resources\BalanceStatusResource;
use App\Models\Balance;
use App\Models\Rate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Database\DatabaseManager as DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BalanceController extends Controller
{
    public static function show(ShowRequest $request, Cache $cache)
    {
        $validated = $request->validated();
    
        $balance = $cache->rememberForever('balances:id_'.$validated['id'], function () use ($validated) {
            try {
                $balance = Balance::findOrFail($validated['id']);
            } catch (ModelNotFoundException $e) {
                throw new ModelNotFoundException('Balance not found');
            }
            return $balance;
        });
    
        return new BalanceStatusResource($balance);
    }

    public static function update(UpdateRequest $request, Cache $cache , DB $db)
    {
        $validated = $request->validated();

        $balance = $cache->rememberForever('balances:id_'.$validated['id'], function () use ($validated) {
            try {
                $balance = Balance::findOrFail($validated['id']);
            } catch (ModelNotFoundException $e) {
                throw new ModelNotFoundException('Balance not found');
            }
            return $balance;
        });

        $transactionData = TransactionHelper::transformToTransactionArray($validated);

        $rate = new Rate();
        $currentRate = $rate->getCurrentRate();

        if($transactionData['currency']!='usd'){
            $currentRateValue = $currentRate->{$validated['currency']."_rate"};
            $convertedAmmount = floatval($validated['amount']) / $currentRateValue ;
        } else {
            $convertedAmmount = $validated['amount'];
        }

        if($validated['transaction']=='debit'){
            $balance->usd = $balance->usd + $convertedAmmount;
        } elseif ($validated['transaction']=='credit' && ($balance->usd - $convertedAmmount) > 0){
            $balance->usd = $balance->usd - $convertedAmmount;
        } else {
            return ResponseHelper::createErrorResponse('Insufficient balance to perform the debit transaction.', 400);
        }

        // DB Transaction secure
        try {
            $transaction = $db->transaction(function () use ($balance, $transactionData, $cache, $validated) {
                $transaction = $balance->newTransaction($transactionData);
                $balance->save();

                $cache->put('balances:id_'.$validated['id'], $balance);
                $cache->rememberForever('transactions:id_'.$transaction->id, function () use ($transaction) {
                    return $transaction;
                });
                return $transaction;
            });
        } catch (\Exception $e) {
            throw new HttpException(500, 'Database error in balance update and transaction creation.');
        }

        return TransactionHelper::createTransactionResponse($transaction, $balance);
    }
}
