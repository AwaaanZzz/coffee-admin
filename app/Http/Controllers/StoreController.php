<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $query = Store::with(['stockBatches.coffeeType', 'coffeePrices']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('penanggung_jawab', 'like', "%{$search}%");
            });
        }

        $stores = $query->orderBy('name')->paginate(20)->withQueryString();
        $allStoresForMap = Store::with(['stockBatches'])->orderBy('name')->get();
        
        $storesMapData = $allStoresForMap->map(function($s) {
            return [
                'id' => $s->id,
                'name' => $s->name,
                'alamat' => $s->alamat ?? 'Alamat belum diisi',
                'penanggung_jawab' => $s->penanggung_jawab ?? '-',
                'latitude' => $s->latitude,
                'longitude' => $s->longitude,
                'has_coords' => $s->has_coordinates,
                'batches_count' => $s->stockBatches->count(),
                'show_url' => route('stores.show', $s),
                'gmaps_url' => $s->google_maps_url,
            ];
        });

        return view('stores.index', compact('stores', 'allStoresForMap', 'storesMapData'));
    }

    public function create()
    {
        return view('stores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tgl_kerjasama' => 'required|date',
            'alamat' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'penanggung_jawab' => 'nullable|string|max:255',
        ]);

        Store::create($validated);

        return redirect()->route('stores.index')->with('success', 'Toko mitra berhasil ditambahkan.');
    }

    public function show(Store $store)
    {
        $store->load('coffeePrices.coffeeType', 'stockBatches.coffeeType');

        $salesData = \App\Models\Sale::where('store_id', $store->id)
            ->selectRaw('coffee_type_id, SUM(jumlah) as qty, SUM(total) as revenue')
            ->groupBy('coffee_type_id')
            ->get()->keyBy('coffee_type_id');

        $batchData = \App\Models\StockBatch::where('store_id', $store->id)
            ->selectRaw('coffee_type_id, SUM(laku) as total_laku, SUM(jumlah_stock - laku) as total_sisa')
            ->groupBy('coffee_type_id')
            ->get()->keyBy('coffee_type_id');

        $allCoffeeIds = $salesData->keys()->merge($batchData->keys())->unique();
        $coffeeTypes = \App\Models\CoffeeType::all()->keyBy('id');

        $topProducts = [];
        foreach ($allCoffeeIds as $cid) {
            $coffee = $coffeeTypes->get($cid);
            if (!$coffee) continue;
            $saleQty = (int) ($salesData->get($cid)?->qty ?? 0);
            $saleRev = (float) ($salesData->get($cid)?->revenue ?? 0);
            $batchQty = (int) ($batchData->get($cid)?->total_laku ?? 0);
            $qty = max($saleQty, $batchQty);
            $price = (float) ($store->coffeePrices->firstWhere('coffee_type_id', $cid)?->price ?? 0);
            $revenue = $saleRev > 0 ? $saleRev : ($qty * $price);
            $topProducts[] = [
                'name' => $coffee->name,
                'category' => $coffee->category,
                'qty' => $qty,
                'revenue' => $revenue,
                'sisa' => (int) ($batchData->get($cid)?->total_sisa ?? 0),
            ];
        }
        usort($topProducts, fn($a, $b) => $b['qty'] <=> $a['qty'] ?: $b['revenue'] <=> $a['revenue']);

        return view('stores.show', compact('store', 'topProducts'));
    }

    public function edit(Store $store)
    {
        return view('stores.edit', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tgl_kerjasama' => 'required|date',
            'alamat' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'penanggung_jawab' => 'nullable|string|max:255',
        ]);

        $store->update($validated);

        return redirect()->route('stores.show', $store)->with('success', 'Data toko mitra dan koordinat peta berhasil diupdate.');
    }

    public function destroy(Store $store)
    {
        $store->delete();
        return redirect()->route('stores.index')->with('success', 'Toko berhasil dihapus.');
    }
}
