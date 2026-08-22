<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_batch_id',
        'type', // tambah | update | tarik | ganti
        'jumlah',
        'keterangan',
        'user_id',
    ];

    public function stockBatch()
    {
        return $this->belongsTo(StockBatch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
