<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'coffee_type_id',
        'stock_batch_id',
        'jumlah',
        'harga',
        'total',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function coffeeType()
    {
        return $this->belongsTo(CoffeeType::class);
    }

    public function stockBatch()
    {
        return $this->belongsTo(StockBatch::class);
    }
}
