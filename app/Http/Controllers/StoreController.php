<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::with(['stockBatches.coffeeType', 'coffeePrices'])->orderBy('name')->paginate(20);
        return view('stores.index', compact('stores'));
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
            'penanggung_jawab' => 'nullable|string|max:255',
        ]);

        Store::create($validated);

        return redirect()->route('stores.index')->with('success', 'Toko berhasil ditambahkan.');
    }

    public function show(Store $store)
    {
        $store->load('coffeePrices.coffeeType', 'stockBatches.coffeeType');
        return view('stores.show', compact('store'));
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
            'penanggung_jawab' => 'nullable|string|max:255',
        ]);

        $store->update($validated);

        return redirect()->route('stores.index')->with('success', 'Toko berhasil diupdate.');
    }

    public function destroy(Store $store)
    {
        $store->delete();
        return redirect()->route('stores.index')->with('success', 'Toko berhasil dihapus.');
    }
}
