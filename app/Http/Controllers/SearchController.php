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
            ->limit(5)->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->name,
                    'subtitle' => 'Toko',
                    'url' => route('stores.show', $item->id ?? 1), // Adjust fallback as needed
                    'icon' => 'store'
                ];
            });

        $coffeeTypes = CoffeeType::where('name', 'like', "%{$q}%")
            ->limit(5)->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->name,
                    'subtitle' => 'Jenis Kopi',
                    'url' => route('coffee_types.show', $item->id ?? 1),
                    'icon' => 'coffee'
                ];
            });

        $stocks = StockBatch::where('kode_produksi', 'like', "%{$q}%")
            ->limit(5)->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->kode_produksi,
                    'subtitle' => 'Batch Stock',
                    'url' => route('stock.show', $item->id ?? 1),
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
