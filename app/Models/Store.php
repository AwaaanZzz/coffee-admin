<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tgl_kerjasama',
        'alamat',
        'penanggung_jawab',
    ];

    protected $casts = [
        'tgl_kerjasama' => 'date',
    ];

    public function coffeePrices()
    {
        return $this->hasMany(StoreCoffeePrice::class);
    }

    public function stockBatches()
    {
        return $this->hasMany(StockBatch::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function financeReports()
    {
        return $this->hasMany(FinanceReport::class);
    }
}
