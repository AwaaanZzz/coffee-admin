<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\CoffeeType;
use App\Models\StockBatch;
use App\Models\Sale;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->get('q', '');
        
        if (empty($q)) {
            return response()->json(['stores' => [], 'coffeeTypes' => [], 'stocks' => []]);
        }

        $stores = Store::where('name', 'like', "%{$q}%")
            ->orWhere('penanggung_jawab', 'like', "%{$q}%")
            ->limit(5)->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->name,
                    'subtitle' => 'Toko Mitra' . ($item->penanggung_jawab ? ' (PJ: ' . $item->penanggung_jawab . ')' : ''),
                    'url' => route('stores.show', $item->id),
                    'icon' => 'store'
                ];
            });

        $coffeeTypes = CoffeeType::where('name', 'like', "%{$q}%")
            ->orWhere('category', 'like', "%{$q}%")
            ->limit(5)->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->name,
                    'subtitle' => 'Jenis Kopi (' . ucfirst($item->category) . ')',
                    'url' => route('coffee-types.index'),
                    'icon' => 'coffee'
                ];
            });

        $stocks = StockBatch::with(['coffeeType', 'store'])
            ->where('kode_produksi', 'like', "%{$q}%")
            ->orWhere('barcode', 'like', "%{$q}%")
            ->limit(5)->get()->map(function($item) {
                $coffeeName = $item->coffeeType->name ?? 'Kopi';
                $storeName = $item->store->name ?? 'Gudang';
                return [
                    'id' => $item->id,
                    'title' => $item->kode_produksi . ($item->barcode ? ' [' . $item->barcode . ']' : ''),
                    'subtitle' => $coffeeName . ' @ ' . $storeName . ' (Sisa: ' . $item->sisa_stock . ' pcs)',
                    'url' => route('stock.index', ['search' => $item->kode_produksi]),
                    'icon' => 'package'
                ];
            });

        return response()->json([
            'stores' => $stores,
            'coffeeTypes' => $coffeeTypes,
            'stocks' => $stocks
        ]);
    }
}
