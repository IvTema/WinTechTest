<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $user_id
 * @property string $transaction_type
 * @property integer $amount
 * @property string $currency
 * @property string $issue
 * @property string $created_at
 * @property string $updated_at
 */
class Transaction extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'transaction_type', 'amount', 'currency', 'issue'];

    public function balance(): BelongsTo
    {
        return $this->belongsTo(Balance::class, 'user_id');
    }
}
