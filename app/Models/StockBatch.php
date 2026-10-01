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
        'barcode',
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

    /**
     * Hitung digit cek (Check Digit) GS1 Modulo-10 untuk standar barcode internasional EAN-13.
     */
    public static function calculateEan13CheckDigit(string $digits12): int
    {
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $d = (int) $digits12[$i];
            $sum += ($i % 2 === 0) ? $d * 1 : $d * 3;
        }
        $remainder = $sum % 10;
        return ($remainder === 0) ? 0 : (10 - $remainder);
    }

    /**
     * Generate Kode Barcode Standar Retail Internasional (GS1 EAN-13, 13 Digit Angka Murni).
     * Format: 899 (Indonesia) + YYMM (TahunBulan) + 5 Digit Unik + 1 Check Digit Modulo-10
     * Contoh: 8992609104824
     */
    public static function generateUniqueBarcode(?int $storeId = null, ?int $coffeeTypeId = null): string
    {
        $prefix = '899';
        $yearMonth = Carbon::now()->format('ym');

        do {
            $randomSeq = str_pad((string) mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $first12 = "{$prefix}{$yearMonth}{$randomSeq}";
            $checkDigit = self::calculateEan13CheckDigit($first12);
            $candidate = "{$first12}{$checkDigit}";
        } while (self::where('barcode', $candidate)->exists());

        return $candidate;
    }

    /**
     * Generate Kode Produksi / Nomor Batch Roastery Kustom.
     * Pengguna bebas mengedit dan menambahkan kode produksi sendiri.
     * Default template: HH-{YYMM}-{SEQ} (contoh: HH-2609-001)
     */
    public static function generateUniqueKodeProduksi(string $prefix = 'HH'): string
    {
        $ym = Carbon::now()->format('ym');
        $seq = 1;
        do {
            $candidate = sprintf("%s-%s-%03d", $prefix, $ym, $seq);
            $seq++;
        } while (self::where('kode_produksi', $candidate)->exists());

        return $candidate;
    }

    public static function generateUniqueCustomSku(string $prefix = 'HKH'): string
    {
        return self::generateUniqueKodeProduksi($prefix);
    }
}
