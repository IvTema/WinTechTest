<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $usd
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
}
