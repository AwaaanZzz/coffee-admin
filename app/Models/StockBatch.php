<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StockBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'coffee_type_id',
        'kode_produksi',
        'tgl_stock',
        'tgl_exp',
        'jumlah_stock',
        'laku',
        'status', // normal | tarik | ganti
    ];

    protected $casts = [
        'tgl_stock' => 'date',
        'tgl_exp' => 'date',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function coffeeType()
    {
        return $this->belongsTo(CoffeeType::class);
    }

    public function logs()
    {
        return $this->hasMany(StockLog::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // Sisa stock = jumlah_stock - laku
    public function getSisaAttribute(): int
    {
        return $this->jumlah_stock - $this->laku;
    }

    // Total nilai stock = sisa x harga toko untuk kopi ini
    public function getTotalAttribute(): float
    {
        $price = $this->store?->coffeePrices
            ->firstWhere('coffee_type_id', $this->coffee_type_id)?->price ?? 0;

        return $this->sisa * $price;
    }

    // True kalau exp <= 7 hari lagi (buat highlight merah di frontend)
    public function getIsExpiringSoonAttribute(): bool
    {
        return Carbon::now()->diffInDays($this->tgl_exp, false) <= 7
            && Carbon::now()->diffInDays($this->tgl_exp, false) >= 0;
    }

    public function getIsExpiredAttribute(): bool
    {
        return Carbon::now()->greaterThan($this->tgl_exp);
    }
}
