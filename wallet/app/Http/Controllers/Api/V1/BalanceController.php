<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ResponseHelper;
use App\Helpers\TransactionHelper;
use App\Helpers\ValidationHelper;
use App\Helpers\ValidationUpdateRuleHelper;
use App\Helpers\ValidationIndexRuleHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\BalanceStatusResource;
use App\Models\Balance;
use App\Models\Rate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class BalanceController extends Controller
{
    public static function index(Request $request)
    {
        $indexRuleHelper = new ValidationIndexRuleHelper();
        $validator = Validator::make($request->all(), $indexRuleHelper->getRules());
        $validationResponse = ValidationHelper::validateOrDropError($validator);
        if ($validationResponse !== null) {
            return $validationResponse;
        } 
        $validated = $validator->validated();

        $balance = Cache::rememberForever('balances:id_'.$validated['id'], function () use ($validated) {
            return Balance::find($validated['id']);
        });

        $checker = ResponseHelper::checkOrDropError($balance);
        if ($checker !== null) {
            return $checker;
        } 

        return new BalanceStatusResource($balance);
    }

    public static function update(Request $request)
    {
        $updateRuleHelper = new ValidationUpdateRuleHelper();
        $validator = Validator::make($request->all(), $updateRuleHelper->getRules());
        $validationResponse = ValidationHelper::validateOrDropError($validator);
        if ($validationResponse !== null) {
            return $validationResponse;
        } 
        $validated = $validator->validated();

        $balance = Cache::rememberForever('balances:id_'.$validated['id'], function () use ($validated) {
            return Balance::find($validated['id']);
        });

        $checker = ResponseHelper::checkOrDropError($balance);
        if ($checker !== null) {
            return $checker;
        } 

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
