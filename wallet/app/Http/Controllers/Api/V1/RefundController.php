<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class RefundController extends Controller{
   public function index(){
      $refunds = DB::select("
      SELECT SUM(amount) AS total_refund_amount
      FROM transactions
      WHERE issue = 'refund'
      AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY);
      ");

      return $refunds;
   }
}
