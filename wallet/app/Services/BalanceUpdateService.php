<?php

namespace App\Services;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Database\DatabaseManager as DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class BalanceUpdateService.
 */
class BalanceUpdateService
{
    protected $db;
    protected $cache;

    public function __construct(DB $db, Cache $cache)
    {
        $this->db = $db;
        $this->cache = $cache;
    }

    public function createTransaction($balance, $transactionData, $validated)
    {
        try {
            $transaction = $this->db->transaction(function () use ($balance, $transactionData, $validated) {
                $transaction = $balance->newTransaction($transactionData);
                $balance->save();

                $this->cache->put('balances:id_'.$validated['id'], $balance);
                $this->cache->rememberForever('transactions:id_'.$transaction->id, function () use ($transaction) {
                    return $transaction;
                });
                return $transaction;
            });
        } catch (\Exception $e) {
            throw new HttpException(500, 'Database error in balance update and transaction creation.'.$e);
        }

        return $transaction;
    }
}
