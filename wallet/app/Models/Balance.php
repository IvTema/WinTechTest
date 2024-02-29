<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property float $usd
 * @property string $created_at
 * @property string $updated_at
 */
class Balance extends Model
{
    use HasFactory;
    /**
     * @var array
     */
    protected $fillable = ['usd'];

    /**
     * Create a new transaction for the balance.
     *
     * @param array $data
     * @return Transaction
     */
    public function newTransaction(array $data): Transaction
    {
        return $this->transactions()->create($data);
    }

    /**
     * Define the relationship between balance and transactions.
     *
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

}
