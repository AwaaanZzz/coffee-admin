<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoffeeType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category', // robusta | arabika
    ];

    public function storePrices()
    {
        return $this->hasMany(StoreCoffeePrice::class);
    }

    public function stockBatches()
    {
        return $this->hasMany(StockBatch::class);
    }
}
