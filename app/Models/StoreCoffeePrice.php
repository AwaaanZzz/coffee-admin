<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreCoffeePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'coffee_type_id',
        'price',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function coffeeType()
    {
        return $this->belongsTo(CoffeeType::class);
    }
}
