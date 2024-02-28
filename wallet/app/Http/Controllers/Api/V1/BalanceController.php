<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BalanceStatusCollection;
use App\Models\Balance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BalanceController extends Controller
{
    public static function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'integer|min:1',
        ]);

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
        
    }
}
