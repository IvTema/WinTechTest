<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Database\DatabaseManager as DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Services\ExtraTransactionDataService;

/**
 * Class BalanceUpdateService.
 */
class BalanceUpdateService
{
    protected $db;
    protected $cache;
    protected $extraData;

    public function __construct(DB $db, Cache $cache, ExtraTransactionDataService $extraData)
    {
        $this->db = $db;
        $this->cache = $cache;
        $this->extraData = $extraData;
    }

    public function createTransaction($balance, $transactionData, $validated, $rate) : Transaction
    {
        try {
            $transaction = $this->db->transaction(function () use ($balance, $transactionData, $validated, $rate) {
                $transactionData = $this->extraData->addBalanceAndRate($transactionData, $balance, $rate);
                $transaction = $balance->newTransaction($transactionData);
                $balance->save();

                $this->cache->put('balances:id_'.$validated['id'], $balance);
                $this->cache->rememberForever('transactions:id_'.$transaction->id, function () use ($transaction) {
                    return $transaction;
                });
                return $transaction;
            });
        } catch (\Exception $e) {
            throw new HttpException(500, 'Database error in balance update and transaction creation. '.$e);
        }

        return $transaction;
    }
}
