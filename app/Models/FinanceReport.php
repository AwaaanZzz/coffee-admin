<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'periode_awal',
        'periode_akhir',
        'pemasukan',
        'pengeluaran',
        'laba',
        'catatan',
    ];

    protected $casts = [
        'periode_awal' => 'date',
        'periode_akhir' => 'date',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
