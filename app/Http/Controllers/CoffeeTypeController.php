<?php

namespace App\Http\Controllers;

use App\Models\CoffeeType;
use App\Models\StockBatch;
use Illuminate\Http\Request;

class CoffeeTypeController extends Controller
{
    public function modalIndex()
    {
        $coffeeTypes = CoffeeType::with(['storePrices.store'])->orderBy('category')->orderBy('name')->get();

        // Ambil data total penjualan per jenis kopi
        $batchSales = StockBatch::selectRaw('coffee_type_id, SUM(laku) as total_laku, SUM(jumlah_stock) as total_stock')
            ->groupBy('coffee_type_id')
            ->get()
            ->keyBy('coffee_type_id');

        $coffeeTypes->each(function ($coffee) use ($batchSales) {
            $prices = $coffee->storePrices->pluck('price')->filter(fn($p) => $p > 0);
            $coffee->avg_price = $prices->count() > 0 ? (float) $prices->avg() : 0;
            $coffee->min_price = $prices->count() > 0 ? (float) $prices->min() : 0;
            $coffee->max_price = $prices->count() > 0 ? (float) $prices->max() : 0;
            $coffee->stores_count = $coffee->storePrices->count();

            $batch = $batchSales->get($coffee->id);
            $coffee->total_laku = (int) ($batch?->total_laku ?? 0);
            $coffee->total_stock = (int) ($batch?->total_stock ?? 0);

            $modal = (float) ($coffee->modal ?? 0);
            $coffee->laba_unit = $coffee->avg_price > 0 ? ($coffee->avg_price - $modal) : 0;
            $coffee->margin_pct = $coffee->avg_price > 0 ? (($coffee->avg_price - $modal) / $coffee->avg_price) * 100 : 0;
            $coffee->total_beban_hpp = $modal * $coffee->total_laku;
        });

        $totalVarian = $coffeeTypes->count();
        $configuredCount = $coffeeTypes->where('modal', '>', 0)->count();
        $avgModal = $configuredCount > 0 ? (float) $coffeeTypes->where('modal', '>', 0)->avg('modal') : 0;
        $activeWithPrice = $coffeeTypes->where('avg_price', '>', 0);
        $avgPrice = $activeWithPrice->count() > 0 ? (float) $activeWithPrice->avg('avg_price') : 0;
        $avgMargin = $avgPrice > 0 ? (($avgPrice - $avgModal) / $avgPrice) * 100 : 0;
        $totalHppSold = $coffeeTypes->sum('total_beban_hpp');

        $kpis = [
            'total_varian' => $totalVarian,
            'configured_count' => $configuredCount,
            'avg_modal' => round($avgModal),
            'avg_price' => round($avgPrice),
            'avg_margin' => round($avgMargin, 1),
            'total_hpp_sold' => $totalHppSold,
        ];

        return view('coffee-types.modal', compact('coffeeTypes', 'kpis'));
    }

    public function modalUpdate(Request $request)
    {
        $validated = $request->validate([
            'modals' => 'required|array',
            'modals.*' => 'nullable|numeric|min:0',
        ]);

        foreach ($validated['modals'] as $coffeeId => $modalValue) {
            CoffeeType::where('id', $coffeeId)->update([
                'modal' => $modalValue !== null && $modalValue !== '' ? (float) $modalValue : 0,
            ]);
        }

        return redirect()->route('coffee-types.modal')->with('success', 'Modal / HPP untuk seluruh jenis kopi berhasil diperbarui! Laporan keuangan di Dashboard kini otomatis menggunakan data riil.');
    }

    public function index()
    {
        $coffeeTypes = CoffeeType::orderBy('category')->orderBy('name')->get();
        return view('coffee-types.index', compact('coffeeTypes'));
    }

    public function create()
    {
        return view('coffee-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:robusta,arabika',
        ]);

        CoffeeType::create($validated);

        return redirect()->route('coffee-types.index')->with('success', 'Jenis kopi berhasil ditambahkan.');
    }

    public function edit(CoffeeType $coffeeType)
    {
        return view('coffee-types.edit', compact('coffeeType'));
    }

    public function update(Request $request, CoffeeType $coffeeType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:robusta,arabika',
        ]);

        $coffeeType->update($validated);

        return redirect()->route('coffee-types.index')->with('success', 'Jenis kopi berhasil diupdate.');
    }

    public function destroy(CoffeeType $coffeeType)
    {
        $coffeeType->delete();
        return redirect()->route('coffee-types.index')->with('success', 'Jenis kopi berhasil dihapus.');
    }
}
