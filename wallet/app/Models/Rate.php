<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property float $rub_rate
 * @property string $created_at
 * @property string $updated_at
 */
class Rate extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = ['rub_rate'];


    public function getCurrentRate(): Rate
    {
        $latestRateModel = $this->latest('id')->first();
        return $latestRateModel;
    }
}
