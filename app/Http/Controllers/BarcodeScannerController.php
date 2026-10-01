<?php

namespace App\Http\Controllers;

use App\Models\CoffeeType;
use App\Models\StockBatch;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BarcodeScannerController extends Controller
{
    /**
     * Tampilkan halaman Terminal Scanner Barcode (Model T120).
     */
    public function index()
    {
        $stores = Store::orderBy('name')->get();
        $totalBatches = StockBatch::where('status', '!=', 'tarik')->count();
        $expiringBatches = StockBatch::where('status', '!=', 'tarik')
            ->whereBetween('tgl_exp', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->count();

        return view('scanner.index', compact('stores', 'totalBatches', 'expiringBatches'));
    }

    /**
     * Endpoint API Lookup Barcode (AJAX fast response untuk hardware T120).
     */
    public function lookup(Request $request): JsonResponse
    {
        $code = trim($request->input('code', ''));

        if (empty($code)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode barcode tidak boleh kosong.',
            ], 422);
        }

        // Cari batch berdasarkan kode produksi (EAN-13 atau format sebelumnya)
        // Kita juga bisa mendukung filter toko mitra jika dipilih di UI
        $query = StockBatch::with(['store.coffeePrices', 'coffeeType'])
            ->where('status', '!=', 'tarik');

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Cari batch berdasarkan barcode (EAN-13) atau kode produksi kustom
        $batch = (clone $query)->where(function($q) use ($code) {
            $q->where('barcode', $code)
              ->orWhere('kode_produksi', $code);
        })->first();

        if (!$batch) {
            $batch = (clone $query)->where(function($q) use ($code) {
                $q->where('barcode', 'like', "%{$code}%")
                  ->orWhere('kode_produksi', 'like', "%{$code}%");
            })->first();
        }

        if (!$batch) {
            return response()->json([
                'success' => false,
                'message' => "Kode / Barcode [{$code}] tidak ditemukan dalam database inventaris.",
                'code' => $code,
            ], 404);
        }

        // Resolusi Harga Jual Toko Mitra
        $store = $batch->store;
        $coffeeType = $batch->coffeeType;

        $storePrice = $store?->coffeePrices
            ?->firstWhere('coffee_type_id', $batch->coffee_type_id)
            ?->price;

        $sellingPrice = (float) ($storePrice ?? 0);
        $modalPrice = (float) ($coffeeType?->modal ?? 0);

        // Kalkulasi Status Expired & Hari Tersisa
        $today = Carbon::now()->startOfDay();
        $expDate = Carbon::parse($batch->tgl_exp)->startOfDay();
        $diffDays = $today->diffInDays($expDate, false);

        if ($diffDays < 0) {
            $freshnessStatus = 'expired';
            $freshnessLabel = 'KADALUARSA (EXPIRED)';
            $freshnessClass = 'danger';
            $freshnessDesc = 'Lewat ' . abs($diffDays) . ' hari yang lalu';
        } elseif ($diffDays <= 7) {
            $freshnessStatus = 'warning';
            $freshnessLabel = 'MENDEKATI KADALUARSA';
            $freshnessClass = 'warning';
            $freshnessDesc = $diffDays === 0 ? 'Hari ini tanggal kadaluarsa!' : 'Tersisa ' . $diffDays . ' hari lagi';
        } else {
            $freshnessStatus = 'fresh';
            $freshnessLabel = 'SEGAR & AMAN';
            $freshnessClass = 'success';
            $freshnessDesc = 'Tersisa ' . $diffDays . ' hari lagi';
        }

        $sisaStok = $batch->sisa;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $batch->id,
                'barcode' => $batch->barcode,
                'kode_produksi' => $batch->kode_produksi,
                'coffee_name' => $coffeeType?->name ?? 'Kopi Tanpa Nama',
                'coffee_category' => strtoupper($coffeeType?->category ?? 'ROBUSTA'),
                'store_name' => $store?->name ?? 'Pusat Roastery',
                'store_id' => $store?->id,
                'tgl_exp' => $batch->tgl_exp->format('d/m/Y'),
                'tgl_exp_raw' => $batch->tgl_exp->toDateString(),
                'tgl_stock' => $batch->tgl_stock ? $batch->tgl_stock->format('d/m/Y') : '-',
                'days_remaining' => $diffDays,
                'freshness_status' => $freshnessStatus,
                'freshness_label' => $freshnessLabel,
                'freshness_class' => $freshnessClass,
                'freshness_desc' => $freshnessDesc,
                'harga_jual' => $sellingPrice,
                'harga_jual_formatted' => 'Rp ' . number_format($sellingPrice, 0, ',', '.'),
                'modal' => $modalPrice,
                'modal_formatted' => 'Rp ' . number_format($modalPrice, 0, ',', '.'),
                'jumlah_stock' => (int) $batch->jumlah_stock,
                'laku' => (int) $batch->laku,
                'sisa' => $sisaStok,
                'is_low_stock' => $sisaStok > 0 && $sisaStok <= 3,
                'is_out_of_stock' => $sisaStok <= 0,
                'scanned_at' => Carbon::now()->format('H:i:s'),
            ],
        ]);
    }

    /**
     * Quick Action: Catat 1 pack terjual langsung dari terminal scanner
     */
    public function recordSale(Request $request): JsonResponse
    {
        $batchId = $request->input('batch_id');
        $batch = StockBatch::with(['store.coffeePrices', 'coffeeType'])->find($batchId);

        if (!$batch) {
            return response()->json(['success' => false, 'message' => 'Batch tidak ditemukan.'], 404);
        }

        if ($batch->sisa <= 0) {
            return response()->json(['success' => false, 'message' => 'Stok fisik batch ini sudah habis (0 pcs).'], 422);
        }

        $store = $batch->store;
        $price = (float) ($store?->coffeePrices?->firstWhere('coffee_type_id', $batch->coffee_type_id)?->price ?? 0);

        // Increment laku
        $batch->increment('laku', 1);

        // Create Sale transaction
        \App\Models\Sale::create([
            'store_id' => $batch->store_id,
            'coffee_type_id' => $batch->coffee_type_id,
            'stock_batch_id' => $batch->id,
            'jumlah' => 1,
            'harga' => $price,
            'total' => $price,
            'tanggal' => now()->toDateString(),
        ]);

        \App\Models\StockLog::create([
            'stock_batch_id' => $batch->id,
            'user_id' => auth()->id(),
            'type' => 'update',
            'jumlah' => 1,
            'keterangan' => 'Penjualan Terminal Scanner Barcode (' . $batch->barcode . ')',
        ]);

        $batch->refresh();

        return response()->json([
            'success' => true,
            'message' => "Berhasil! 1 Pack {$batch->coffeeType?->name} dicatat laku terjual.",
            'new_sisa' => $batch->sisa,
            'new_laku' => $batch->laku,
        ]);
    }
}
