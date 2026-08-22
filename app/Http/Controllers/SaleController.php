<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StockBatch;
use App\Models\Sale;
use App\Models\StockLog;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['store', 'coffeeType']);

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        $sales = $query->orderByDesc('tanggal')->paginate(20)->withQueryString();
        $stores = Store::orderBy('name')->get();

        return view('sales.index', compact('sales', 'stores'));
    }

    public function create()
    {
        $stores = Store::orderBy('name')->get();
        return view('sales.create', compact('stores'));
    }

    // Dipanggil via AJAX/JS: ambil batch stock yang tersedia (sisa > 0) untuk toko tertentu
    public function availableStock(Store $store)
    {
        $batches = StockBatch::with('coffeeType')
            ->where('store_id', $store->id)
            ->where('status', '!=', 'tarik')
            ->get()
            ->filter(fn ($b) => $b->sisa > 0)
            ->map(function ($b) {
                $price = $b->store->coffeePrices->firstWhere('coffee_type_id', $b->coffee_type_id)?->price ?? 0;
                return [
                    'id' => $b->id,
                    'label' => "{$b->coffeeType->name} (Sisa: {$b->sisa}, Kode: {$b->kode_produksi})",
                    'sisa' => $b->sisa,
                    'price' => $price,
                    'coffee_type_id' => $b->coffee_type_id,
                ];
            })->values();

        return response()->json($batches);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'stock_batch_id' => 'required|exists:stock_batches,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal' => 'required|date',
        ]);

        $batch = StockBatch::findOrFail($validated['stock_batch_id']);

        if ($validated['jumlah'] > $batch->sisa) {
            return back()->withErrors(['jumlah' => 'Jumlah melebihi sisa stock (' . $batch->sisa . ').'])->withInput();
        }

        $price = $batch->store->coffeePrices->firstWhere('coffee_type_id', $batch->coffee_type_id)?->price ?? 0;

        Sale::create([
            'store_id' => $batch->store_id,
            'coffee_type_id' => $batch->coffee_type_id,
            'stock_batch_id' => $batch->id,
            'jumlah' => $validated['jumlah'],
            'harga' => $price,
            'total' => $price * $validated['jumlah'],
            'tanggal' => $validated['tanggal'],
        ]);

        $batch->increment('laku', $validated['jumlah']);

        StockLog::create([
            'stock_batch_id' => $batch->id,
            'type' => 'update',
            'jumlah' => $validated['jumlah'],
            'keterangan' => 'Penjualan tanggal ' . $validated['tanggal'],
        ]);

        return redirect()->route('sales.index')->with('success', 'Penjualan berhasil dicatat.');
    }

    public function destroy(Sale $sale)
    {
        // Kembalikan laku di stock batch
        $sale->stockBatch->decrement('laku', $sale->jumlah);
        $sale->delete();

        return redirect()->route('sales.index')->with('success', 'Data penjualan dihapus & stock dikembalikan.');
    }
}
