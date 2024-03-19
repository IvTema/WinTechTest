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
use App\Services\BalanceUpdateService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Cache\Repository as Cache;

class BalanceController extends Controller
{
    protected $balanceUpdateService;

    public function __construct(BalanceUpdateService $balanceUpdateService)
    {
        $this->balanceUpdateService = $balanceUpdateService;
    }

    public static function index(IndexBalanceRequest $request, Cache $cache)
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

    public function update(UpdateBalanceRequest $request, Cache $cache)
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
            $convertedAmmount = intval($validated['amount']) / $currentRateValue ;
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
        $transaction = $this->balanceUpdateService->createTransaction($balance, $transactionData, $validated);

        return TransactionHelper::createTransactionResponse($transaction, $balance);
    }
}
