<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\CoffeeType;
use App\Models\StockBatch;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StockBatchController extends Controller
{
    public function index(Request $request)
    {
        $query = StockBatch::with(['store', 'coffeeType']);

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('barcode', 'like', "%{$search}%")
                  ->orWhere('kode_produksi', 'like', "%{$search}%")
                  ->orWhereHas('coffeeType', fn ($c) => $c->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('store', fn ($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        $stockBatches = $query->orderByDesc('id')->paginate(20)->withQueryString();
        $stores = Store::orderBy('name')->get();

        return view('stock.index', compact('stockBatches', 'stores'));
    }

    public function create()
    {
        $stores = Store::orderBy('name')->get();
        $coffeeTypes = CoffeeType::orderBy('category')->orderBy('name')->get();
        $defaultBarcode = StockBatch::generateUniqueBarcode();
        $defaultKodeProduksi = StockBatch::generateUniqueKodeProduksi();

        return view('stock.create', compact('stores', 'coffeeTypes', 'defaultBarcode', 'defaultKodeProduksi'));
    }

    public function generateCode(Request $request)
    {
        $type = $request->input('type', 'barcode');
        if ($type === 'kode_produksi' || $type === 'sku') {
            $prefix = strtoupper(trim($request->input('prefix', 'HH')));
            if (empty($prefix)) $prefix = 'HH';
            $code = StockBatch::generateUniqueKodeProduksi($prefix);
        } else {
            $code = StockBatch::generateUniqueBarcode();
        }

        return response()->json(['code' => $code, 'type' => $type]);
    }

    public function printLabels(Request $request)
    {
        $query = StockBatch::with(['store', 'coffeeType']);

        if ($request->filled('batch_id')) {
            $query->where('id', $request->batch_id);
        } elseif ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        $batches = $query->orderByDesc('id')->get();

        return view('stock.print-labels', compact('batches'));
    }

    public function regenerateDuplicates()
    {
        $batches = StockBatch::all();
        $seen = [];
        $updatedCount = 0;

        foreach ($batches as $batch) {
            $val = trim($batch->barcode);
            if (empty($val) || isset($seen[$val]) || !preg_match('/^899\d{10}$/', $val)) {
                $batch->barcode = StockBatch::generateUniqueBarcode();
                $batch->save();
                $updatedCount++;
            }
            $seen[$batch->barcode] = true;
        }

        return redirect()->route('stock.index')->with('success', "{$updatedCount} barcode produksi berhasil diverifikasi ke Standar Internasional GS1 EAN-13 (13 Digit).");
    }

    public function store(Request $request)
    {
        if (empty($request->barcode)) {
            $request->merge([
                'barcode' => StockBatch::generateUniqueBarcode(),
            ]);
        }
        if (empty($request->kode_produksi)) {
            $request->merge([
                'kode_produksi' => StockBatch::generateUniqueKodeProduksi(),
            ]);
        }

        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'coffee_type_id' => 'required|exists:coffee_types,id',
            'barcode' => 'required|string|size:13|unique:stock_batches,barcode',
            'kode_produksi' => 'required|string|max:100',
            'tgl_stock' => 'required|date',
            'tgl_exp' => 'required|date|after:tgl_stock',
            'jumlah_stock' => 'required|integer|min:1',
        ], [
            'barcode.unique' => 'Barcode ini sudah digunakan oleh produk lain.',
            'barcode.size' => 'Barcode harus 13 digit angka standar GS1 EAN-13.',
            'kode_produksi.required' => 'Kode produksi wajib diisi (bisa diedit sesuai batch roastery Anda).',
        ]);

        $batch = StockBatch::create($validated);

        StockLog::create([
            'stock_batch_id' => $batch->id,
            'type' => 'tambah',
            'jumlah' => $batch->jumlah_stock,
            'keterangan' => 'Stock awal masuk (Kode Produksi: ' . $batch->kode_produksi . ', Barcode: ' . $batch->barcode . ')',
        ]);

        return redirect()->route('stock.index')->with('success', 'Stock berhasil ditambahkan dengan Kode Produksi: ' . $batch->kode_produksi . ' & Barcode: ' . $batch->barcode);
    }

    public function edit(StockBatch $stock)
    {
        return view('stock.edit', ['batch' => $stock]);
    }

    // Update stock batch: barcode, kode_produksi, tgl_stock, tgl_exp, jumlah_stock, laku & status tarik/ganti
    public function update(Request $request, StockBatch $stock)
    {
        $validated = $request->validate([
            'barcode' => [
                'required',
                'string',
                'size:13',
                Rule::unique('stock_batches', 'barcode')->ignore($stock->id),
            ],
            'kode_produksi' => 'required|string|max:100',
            'tgl_stock' => 'required|date',
            'tgl_exp' => 'required|date|after:tgl_stock',
            'jumlah_stock' => 'required|integer|min:1',
            'laku' => 'required|integer|min:0',
            'status' => 'required|in:normal,tarik,ganti',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'barcode.required' => 'Barcode wajib diisi.',
            'barcode.size' => 'Barcode harus 13 digit angka standar GS1 EAN-13.',
            'barcode.unique' => 'Barcode ini sudah digunakan oleh batch lain.',
            'kode_produksi.required' => 'Kode produksi wajib diisi (bisa diedit bebas).',
            'tgl_stock.required' => 'Tanggal Masuk Stock wajib diisi.',
            'tgl_exp.required' => 'Tanggal Expired wajib diisi.',
            'tgl_exp.after' => 'Tanggal Expired harus setelah Tanggal Masuk Stock.',
            'jumlah_stock.min' => 'Jumlah stock minimal 1.',
        ]);

        if ($validated['laku'] > $validated['jumlah_stock']) {
            return back()->withErrors(['laku' => 'Jumlah laku (' . $validated['laku'] . ') tidak boleh melebihi jumlah stock (' . $validated['jumlah_stock'] . ').'])->withInput();
        }

        $oldKode = $stock->kode_produksi;
        $oldBarcode = $stock->barcode;
        $newKode = trim($validated['kode_produksi']);
        $newBarcode = trim($validated['barcode']);

        $stock->update([
            'barcode' => $newBarcode,
            'kode_produksi' => $newKode,
            'tgl_stock' => $validated['tgl_stock'],
            'tgl_exp' => $validated['tgl_exp'],
            'jumlah_stock' => $validated['jumlah_stock'],
            'laku' => $validated['laku'],
            'status' => $validated['status'],
        ]);

        $ket = $validated['keterangan'] ?? 'Update batch';
        if ($oldKode !== $newKode) {
            $ket .= " [Kode Prod diubah: {$oldKode} -> {$newKode}]";
        }
        if ($oldBarcode !== $newBarcode) {
            $ket .= " [Barcode diubah: {$oldBarcode} -> {$newBarcode}]";
        }

        StockLog::create([
            'stock_batch_id' => $stock->id,
            'type' => 'update',
            'jumlah' => $validated['laku'],
            'keterangan' => $ket . ' (tgl masuk: ' . date('d/m/Y', strtotime($validated['tgl_stock'])) . ', tgl exp: ' . date('d/m/Y', strtotime($validated['tgl_exp'])) . ')',
        ]);

        return redirect()->route('stock.index')->with('success', 'Data stock Kode Produksi ' . $newKode . ' (Barcode: ' . $newBarcode . ') berhasil diperbarui.');
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
