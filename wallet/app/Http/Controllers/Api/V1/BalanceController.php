<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\TransactionHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\BalanceStatusCollection;
use App\Models\Balance;
use App\Models\Rate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BalanceController extends Controller
{
    public static function index(Request $request)
    {
        $validator = Validator::make($request->all(), ValidationHelper::getBalanceUpdateRules());

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Bad Request',
                'message' => 'Invalid data provided',
            ], 400);
        }
        else {
            $validated = $validator->validated();

            $requiredID[] = ['id', $validated['id']];

            $query = Balance::find($requiredID);

            if ($query->count() === 0) {
            return response()->json([
                'error' => 'Not found',
                'message' => 'Data not provided',
            ], 404);
            }

            return new BalanceStatusCollection($query);
        }
    }

    public static function update(Request $request)
    {
        $validator = Validator::make($request->all(), ValidationHelper::getBalanceUpdateRules());

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Bad Request',
                'message' => 'Invalid data provided',
            ], 400);
        }
        
        $validated = $validator->validated();

        $balance = Balance::find($validated['id']);
        if ($balance->count() === 0) {
            return response()->json([
                'error' => 'Not found',
                'message' => 'Data not provided',
            ], 404);
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
        } elseif ($validated['transaction']=='credit'){
            $balance->usd = $balance->usd - $convertedAmmount;
        }

        // DB Transaction secure
        DB::beginTransaction();
        try {
            $transaction = $balance->newTransaction($transactionData);
            $balance->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Internal Server Error',
                'message' => 'Failed to update balance. Please try again later.',
            ], 500);
        }

        return TransactionHelper::createTransactionResponse($transaction, $balance);
    }
}
