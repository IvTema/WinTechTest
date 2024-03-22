<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ResponseHelper;
use App\Helpers\TransactionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexBalanceRequest;
use App\Http\Requests\UpdateBalanceRequest;
use App\Http\Resources\BalanceStatusResource;
use App\Models\Balance;
use App\Services\BalanceUpdateService;
use App\Services\ConvertService;
use App\Services\OperationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Cache\Repository as Cache;

class BalanceController extends Controller
{
    protected $balanceUpdateService;
    protected $convertService;
    protected $operationService;

    public function __construct(BalanceUpdateService $balanceUpdateService, ConvertService $convertService, OperationService $operationService)
    {
        $this->balanceUpdateService = $balanceUpdateService;
        $this->convertService = $convertService;
        $this->operationService = $operationService;
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

        $convertationValue = $this->convertService->convertCurrency($transactionData, $validated);
        $convertationRate = $convertationValue['rate'];
        $convertedAmount = $convertationValue['amount'];

        try {
            $balance = $this->operationService->executeOperation($validated, $balance, $convertedAmount);
        } catch (\Exception $e) {
            return ResponseHelper::createErrorResponse($e->getMessage(), 400);
        }

        // DB Transaction secure
        $transaction = $this->balanceUpdateService->createTransaction($balance, $transactionData, $validated, $convertationRate);

        return TransactionHelper::createTransactionResponse($transaction, $balance);
    }
}
