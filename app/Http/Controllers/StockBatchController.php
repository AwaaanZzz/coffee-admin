<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\CoffeeType;
use App\Models\StockBatch;
use App\Models\StockLog;
use Illuminate\Http\Request;

class StockBatchController extends Controller
{
    public function index(Request $request)
    {
        $query = StockBatch::with(['store', 'coffeeType']);

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        $stockBatches = $query->orderBy('tgl_exp')->paginate(20)->withQueryString();
        $stores = Store::orderBy('name')->get();

        return view('stock.index', compact('stockBatches', 'stores'));
    }

    public function create()
    {
        $stores = Store::orderBy('name')->get();
        $coffeeTypes = CoffeeType::orderBy('category')->orderBy('name')->get();

        return view('stock.create', compact('stores', 'coffeeTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'coffee_type_id' => 'required|exists:coffee_types,id',
            'kode_produksi' => 'required|string|max:255',
            'tgl_stock' => 'required|date',
            'tgl_exp' => 'required|date|after:tgl_stock',
            'jumlah_stock' => 'required|integer|min:1',
        ]);

        $batch = StockBatch::create($validated);

        StockLog::create([
            'stock_batch_id' => $batch->id,
            'type' => 'tambah',
            'jumlah' => $batch->jumlah_stock,
            'keterangan' => 'Stock awal masuk',
        ]);

        return redirect()->route('stock.index')->with('success', 'Stock berhasil ditambahkan.');
    }

    public function edit(StockBatch $stock)
    {
        return view('stock.edit', ['batch' => $stock]);
    }

    // Update laku (terjual) & status tarik/ganti
    public function update(Request $request, StockBatch $stock)
    {
        $validated = $request->validate([
            'laku' => 'required|integer|min:0|max:' . $stock->jumlah_stock,
            'status' => 'required|in:normal,tarik,ganti',
            'keterangan' => 'nullable|string',
        ]);

        $stock->update([
            'laku' => $validated['laku'],
            'status' => $validated['status'],
        ]);

        StockLog::create([
            'stock_batch_id' => $stock->id,
            'type' => 'update',
            'jumlah' => $validated['laku'],
            'keterangan' => $validated['keterangan'] ?? 'Update laku/status',
        ]);

        return redirect()->route('stock.index')->with('success', 'Stock berhasil diupdate.');
    }

    // Tambah stock ke batch yang sudah ada
    public function tambahStock(Request $request, StockBatch $stock)
    {
        $validated = $request->validate([
            'jumlah_tambahan' => 'required|integer|min:1',
        ]);

        $stock->increment('jumlah_stock', $validated['jumlah_tambahan']);

        StockLog::create([
            'stock_batch_id' => $stock->id,
            'type' => 'tambah',
            'jumlah' => $validated['jumlah_tambahan'],
            'keterangan' => 'Tambah stock',
        ]);

        return redirect()->route('stock.index')->with('success', 'Stock berhasil ditambahkan.');
    }

    public function destroy(StockBatch $stock)
    {
        $stock->delete();
        return redirect()->route('stock.index')->with('success', 'Data stock berhasil dihapus.');
    }
}
