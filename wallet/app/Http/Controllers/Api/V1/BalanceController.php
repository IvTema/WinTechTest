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
use Illuminate\Http\JsonResponse;

class BalanceController extends Controller 
{
    public function __construct(
        protected BalanceUpdateService $balanceUpdateService,
        protected ConvertService $convertService,
        protected OperationService $operationService
    ){}

    protected function getBalance(int $id, Cache $cache) : Balance
    {
        return $cache->rememberForever('balances:id_'.$id, function () use ($id) {
            try {
                $balance = Balance::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                throw $e('Balance not found');
            }
            return $balance;
        });
    }

    public static function index(IndexBalanceRequest $request, Cache $cache) : BalanceStatusResource
    {
        $validated = $request->validated();
        $balance = self::getBalance($validated['id'], $cache);

        return new BalanceStatusResource($balance);
    }

    public function update(UpdateBalanceRequest $request, Cache $cache) : JsonResponse
    {
        $validated = $request->validated();
        $balance = self::getBalance($validated['id'], $cache);

        $transactionData = TransactionHelper::transformToTransactionArray($validated);
        list($convertationRate, $convertedAmount) = $this->convertService->convertCurrency($transactionData, $validated);
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
