<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ResponseHelper;
use App\Helpers\TransactionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexBalanceRequest;
use App\Http\Requests\UpdateBalanceRequest;
use App\Http\Resources\BalanceStatusResource;
use App\Models\Balance;
use App\Models\Rate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class BalanceController extends Controller
{
    public static function index(IndexBalanceRequest $request)
    {
        $validated = $request->validated();

        $balance = Cache::rememberForever('balances:id_'.$validated['id'], function () use ($validated) {
            $balance = Balance::find($validated['id']);
            if ($balance === null) {
                throw new ModelNotFoundException('Balance not found');
            }
            return $balance;
        });

        $checker = ResponseHelper::checkOrDropError($balance);
        if ($checker !== null) {
            return $checker;
        } 

        return new BalanceStatusResource($balance);
    }

    public static function update(UpdateBalanceRequest $request)
    {
        $validated = $request->validated();

        $balance = Cache::rememberForever('balances:id_'.$validated['id'], function () use ($validated) {
            $balance = Balance::find($validated['id']);
            if ($balance === null) {
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
            return ResponseHelper::InsufficientBalanceError();
        }

        // DB Transaction secure
        DB::beginTransaction();
        try {
            $transaction = $balance->newTransaction($transactionData);
            $balance->save();

            Cache::put('balances:id_'.$validated['id'], $balance);
            Cache::rememberForever('transactions:id_'.$transaction->id, function () use ($transaction) {
                return $transaction;
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            
            return ResponseHelper::ServerBdError();
        }

        return TransactionHelper::createTransactionResponse($transaction, $balance);
    }
}
