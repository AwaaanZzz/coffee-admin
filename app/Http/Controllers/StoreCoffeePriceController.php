<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\CoffeeType;
use App\Models\StoreCoffeePrice;
use Illuminate\Http\Request;

class StoreCoffeePriceController extends Controller
{
    public function edit(Store $store)
    {
        $coffeeTypes = CoffeeType::orderBy('category')->orderBy('name')->get();
        $existingPrices = $store->coffeePrices->keyBy('coffee_type_id');

        return view('stores.prices', compact('store', 'coffeeTypes', 'existingPrices'));
    }

    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'prices' => 'required|array',
            'prices.*' => 'nullable|numeric|min:0',
        ]);

        foreach ($validated['prices'] as $coffeeTypeId => $price) {
            if ($price === null || $price === '') {
                continue;
            }

            StoreCoffeePrice::updateOrCreate(
                ['store_id' => $store->id, 'coffee_type_id' => $coffeeTypeId],
                ['price' => $price]
            );
        }

        return redirect()->route('stores.show', $store)->with('success', 'Harga kopi berhasil diupdate.');
    }
}
