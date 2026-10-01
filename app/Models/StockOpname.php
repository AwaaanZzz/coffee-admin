<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'user_id',
        'tanggal_audit',
        'total_stok_sistem',
        'total_fisik_terhitung',
        'total_selisih_laku',
        'total_nilai_penjualan',
        'catatan',
    ];

    protected $casts = [
        'tanggal_audit' => 'date',
        'total_nilai_penjualan' => 'float',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(StockOpnameItem::class);
    }
}
