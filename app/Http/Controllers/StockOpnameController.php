<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StockBatch;
use App\Models\Sale;
use App\Models\StockLog;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index()
    {
        $stores = Store::withCount([
            'stockBatches as active_batches_count' => function ($q) {
                $q->where('status', '!=', 'tarik')->whereRaw('(jumlah_stock - laku) > 0');
            }
        ])->with(['stockOpnames' => function ($q) {
            $q->latest('tanggal_audit')->limit(1);
        }])->orderBy('name')->get();

        $recentOpnames = StockOpname::with(['store', 'user'])
            ->latest('tanggal_audit')
            ->latest('id')
            ->paginate(10);

        return view('stock-opname.index', compact('stores', 'recentOpnames'));
    }

    public function create(Store $store)
    {
        // Load active and non-withdrawn batches for this store
        $batches = StockBatch::where('store_id', $store->id)
            ->where('status', '!=', 'tarik')
            ->with(['coffeeType'])
            ->orderByRaw('(jumlah_stock - laku) > 0 DESC')
            ->orderBy('kode_produksi')
            ->get();

        $store->load('coffeePrices');

        $auditItems = $batches->map(function ($b) use ($store) {
            $price = (float) ($store->coffeePrices->firstWhere('coffee_type_id', $b->coffee_type_id)?->price ?? 0);
            return [
                'batch_id' => $b->id,
                'barcode' => $b->barcode,
                'kode_produksi' => $b->kode_produksi,
                'coffee_id' => $b->coffee_type_id,
                'coffee_name' => $b->coffeeType->name ?? '-',
                'category' => $b->coffeeType->category ?? 'robusta',
                'tgl_stock' => $b->tgl_stock ? $b->tgl_stock->format('d/m/Y') : '-',
                'tgl_exp' => $b->tgl_exp ? $b->tgl_exp->format('d/m/Y') : '-',
                'is_expiring_soon' => $b->is_expiring_soon,
                'is_expired' => $b->is_expired,
                'stok_sistem' => $b->sisa,
                'harga_satuan' => $price,
            ];
        });

        return view('stock-opname.create', compact('store', 'auditItems'));
    }

    public function submit(Request $request, Store $store)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.batch_id' => 'required|integer',
            'items.*.fisik_terhitung' => 'required|integer|min:0',
            'catatan' => 'nullable|string|max:500',
        ]);

        $store->load('coffeePrices');

        $stockOpname = DB::transaction(function () use ($request, $store) {
            $totalStokSistem = 0;
            $totalFisik = 0;
            $totalSelisih = 0;
            $totalNilaiPenjualan = 0;

            $opname = StockOpname::create([
                'store_id' => $store->id,
                'user_id' => Auth::id(),
                'tanggal_audit' => now()->toDateString(),
                'total_stok_sistem' => 0,
                'total_fisik_terhitung' => 0,
                'total_selisih_laku' => 0,
                'total_nilai_penjualan' => 0,
                'catatan' => $request->catatan,
            ]);

            foreach ($request->items as $itemData) {
                $batch = StockBatch::where('id', $itemData['batch_id'])
                    ->where('store_id', $store->id)
                    ->lockForUpdate()
                    ->first();

                if (!$batch) continue;

                $stokSistem = $batch->sisa;
                $fisikTerhitung = max(0, (int) $itemData['fisik_terhitung']);
                
                // Selisih laku = stok sistem - fisik yang tersisa di rak toko
                // Jika fisik lebih sedikit dari stok sistem, artinya kopi laku terjual
                $selisihLaku = max(0, $stokSistem - $fisikTerhitung);

                $price = (float) ($store->coffeePrices->firstWhere('coffee_type_id', $batch->coffee_type_id)?->price ?? 0);
                $subtotal = $selisihLaku * $price;

                $totalStokSistem += $stokSistem;
                $totalFisik += $fisikTerhitung;
                $totalSelisih += $selisihLaku;
                $totalNilaiPenjualan += $subtotal;

                // Save Opname Item
                StockOpnameItem::create([
                    'stock_opname_id' => $opname->id,
                    'stock_batch_id' => $batch->id,
                    'coffee_type_id' => $batch->coffee_type_id,
                    'stok_sistem' => $stokSistem,
                    'fisik_terhitung' => $fisikTerhitung,
                    'selisih_laku' => $selisihLaku,
                    'harga_satuan' => $price,
                    'subtotal' => $subtotal,
                ]);

                // Jika ada selisih laku, potong stok batch & catat transaksi penjualan otomatis
                if ($selisihLaku > 0) {
                    $batch->increment('laku', $selisihLaku);

                    Sale::create([
                        'store_id' => $store->id,
                        'coffee_type_id' => $batch->coffee_type_id,
                        'stock_batch_id' => $batch->id,
                        'jumlah' => $selisihLaku,
                        'harga' => $price,
                        'total' => $subtotal,
                        'tanggal' => now()->toDateString(),
                    ]);

                    StockLog::create([
                        'stock_batch_id' => $batch->id,
                        'user_id' => Auth::id(),
                        'type' => 'update',
                        'jumlah' => $selisihLaku,
                        'keterangan' => "Audit Stok Opname otomatis (Terjual {$selisihLaku} pcs)",
                    ]);
                }
            }

            $opname->update([
                'total_stok_sistem' => $totalStokSistem,
                'total_fisik_terhitung' => $totalFisik,
                'total_selisih_laku' => $totalSelisih,
                'total_nilai_penjualan' => $totalNilaiPenjualan,
            ]);

            return $opname;
        });

        return redirect()->route('stock-opname.receipt', $stockOpname->id)
            ->with('success', "Stok Opname toko {$store->name} berhasil diselesaikan! Terjual otomatis: {$stockOpname->total_selisih_laku} pcs (Rp " . number_format($stockOpname->total_nilai_penjualan, 0, ',', '.') . ")");
    }

    public function receipt($id)
    {
        $stockOpname = StockOpname::with([
            'store.coffeePrices',
            'user',
            'items.coffeeType',
            'items.stockBatch'
        ])->findOrFail($id);

        return view('stock-opname.receipt', compact('stockOpname'));
    }
}
