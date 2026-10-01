<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StockBatch;
use App\Models\Sale;
use App\Models\FinanceReport;
use App\Models\CoffeeType;
use App\Models\Todo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $totalToko = Store::count();
        $totalStock = StockBatch::sum('jumlah_stock');
        $totalLaku = StockBatch::sum('laku');
        $expiringSoon = StockBatch::whereBetween('tgl_exp', [now(), now()->addDays(7)])->count();

        $totalRevenue = Sale::sum('total');

        $thisWeekRevenue = Sale::where('tanggal', '>=', now()->subDays(7))->sum('total');
        $lastWeekRevenue = Sale::whereBetween('tanggal', [now()->subDays(14), now()->subDays(7)])->sum('total');
        
        $revenueGrowth = 0;
        if ($lastWeekRevenue > 0) {
            $revenueGrowth = (($thisWeekRevenue - $lastWeekRevenue) / $lastWeekRevenue) * 100;
        } elseif ($thisWeekRevenue > 0) {
            $revenueGrowth = 100;
        }

        $topProducts = Sale::selectRaw('coffee_type_id, SUM(jumlah) as total_qty')
            ->groupBy('coffee_type_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->with('coffeeType')
            ->get();

        // Per-Store Best-Selling Products Analysis
        $storesList = Store::with(['coffeePrices.coffeeType'])->orderBy('name')->get();
        $coffeeTypes = CoffeeType::all()->keyBy('id');

        $storeAnalytics = [];
        $allStoreProductsAgg = [];

        foreach ($storesList as $store) {
            $salesData = Sale::where('store_id', $store->id)
                ->selectRaw('coffee_type_id, SUM(jumlah) as qty, SUM(total) as revenue')
                ->groupBy('coffee_type_id')
                ->get()
                ->keyBy('coffee_type_id');

            $batchData = StockBatch::where('store_id', $store->id)
                ->selectRaw('coffee_type_id, SUM(laku) as total_laku, SUM(jumlah_stock - laku) as total_sisa')
                ->groupBy('coffee_type_id')
                ->get()
                ->keyBy('coffee_type_id');

            $allCoffeeIds = $salesData->keys()->merge($batchData->keys())->unique();

            $products = [];
            $totalStoreQty = 0;
            $totalStoreRevenue = 0;

            foreach ($allCoffeeIds as $cid) {
                $coffee = $coffeeTypes->get($cid);
                if (!$coffee) continue;

                $saleQty = (int) ($salesData->get($cid)?->qty ?? 0);
                $saleRev = (float) ($salesData->get($cid)?->revenue ?? 0);

                $batchQty = (int) ($batchData->get($cid)?->total_laku ?? 0);
                $batchSisa = (int) ($batchData->get($cid)?->total_sisa ?? 0);

                $qty = max($saleQty, $batchQty);
                $price = (float) ($store->coffeePrices->firstWhere('coffee_type_id', $cid)?->price ?? 0);
                $revenue = $saleRev > 0 ? $saleRev : ($qty * $price);

                $totalStoreQty += $qty;
                $totalStoreRevenue += $revenue;

                $products[] = [
                    'coffee_id' => $cid,
                    'name' => $coffee->name,
                    'category' => $coffee->category,
                    'price' => $price,
                    'qty' => $qty,
                    'revenue' => $revenue,
                    'sisa_stock' => $batchSisa,
                ];

                if (!isset($allStoreProductsAgg[$cid])) {
                    $allStoreProductsAgg[$cid] = [
                        'coffee_id' => $cid,
                        'name' => $coffee->name,
                        'category' => $coffee->category,
                        'total_qty' => 0,
                        'total_revenue' => 0,
                        'stores_breakdown' => [],
                    ];
                }
                $allStoreProductsAgg[$cid]['total_qty'] += $qty;
                $allStoreProductsAgg[$cid]['total_revenue'] += $revenue;
                $allStoreProductsAgg[$cid]['stores_breakdown'][$store->id] = [
                    'store_name' => $store->name,
                    'qty' => $qty,
                    'revenue' => $revenue,
                ];
            }

            usort($products, fn($a, $b) => $b['qty'] <=> $a['qty'] ?: $b['revenue'] <=> $a['revenue']);

            foreach ($products as $i => &$p) {
                $p['rank'] = $i + 1;
                $p['share_pct'] = $totalStoreQty > 0 ? round(($p['qty'] / $totalStoreQty) * 100, 1) : 0;
            }
            unset($p);

            $topProduct = !empty($products) && $products[0]['qty'] > 0 ? $products[0] : null;

            $storeAnalytics[$store->id] = [
                'store_id' => $store->id,
                'store_name' => $store->name,
                'penanggung_jawab' => $store->penanggung_jawab,
                'total_qty' => $totalStoreQty,
                'total_revenue' => $totalStoreRevenue,
                'top_product' => $topProduct,
                'products' => $products,
            ];
        }

        usort($allStoreProductsAgg, fn($a, $b) => $b['total_qty'] <=> $a['total_qty'] ?: $b['total_revenue'] <=> $a['total_revenue']);

        if ($totalRevenue == 0 && !empty($storeAnalytics)) {
            $totalRevenue = collect($storeAnalytics)->sum('total_revenue');
        }

        if ($topProducts->isEmpty() && !empty($allStoreProductsAgg)) {
            $topProducts = collect(array_slice($allStoreProductsAgg, 0, 5))->map(function ($item) {
                return (object) [
                    'coffeeType' => (object) ['name' => $item['name']],
                    'total_qty' => $item['total_qty'],
                ];
            });
        }

        // Kalkulasi Ringkasan Keuntungan & Kerugian yang Seimbang (Balanced) & Real-time
        $reportsFromDb = FinanceReport::with('store')->latest()->get();
        $profitLossData = collect();

        if ($reportsFromDb->isNotEmpty()) {
            $profitLossData = $reportsFromDb->map(function ($report) {
                $pemasukan = (float) $report->pemasukan;
                $pengeluaran = (float) $report->pengeluaran;
                $laba = $pemasukan - $pengeluaran;
                $margin = $pemasukan > 0 ? ($laba / $pemasukan) * 100 : 0;
                
                return (object) [
                    'store_name' => $report->store->name ?? 'Semua Toko',
                    'periode' => $report->catatan ?? ($report->periode_awal ? Carbon::parse($report->periode_awal)->locale('id')->isoFormat('MMMM YYYY') : 'Bulan Ini'),
                    'pemasukan' => $pemasukan,
                    'pengeluaran' => $pengeluaran,
                    'laba' => $laba,
                    'margin' => round($margin, 1),
                    'status' => $laba >= 0 ? 'Surplus' : 'Defisit',
                ];
            });
        } else {
            // Sinkronkan langsung dengan performa riil penjualan per toko & modal HPP tiap-tiap jenis kopi
            $currentMonth = Carbon::now()->locale('id')->isoFormat('MMMM YYYY');
            foreach ($storeAnalytics as $sa) {
                $pemasukan = (float) $sa['total_revenue'];
                
                // Kalkulasi HPP Pengeluaran riil berdasarkan modal tiap jenis kopi yang terjual
                $totalStoreHpp = 0;
                $hasConfiguredModal = false;

                foreach ($sa['products'] as $prod) {
                    $coffee = $coffeeTypes->get($prod['coffee_id']);
                    $modalUnit = (float) ($coffee?->modal ?? 0);
                    $qty = (int) $prod['qty'];
                    
                    if ($modalUnit > 0) {
                        $totalStoreHpp += ($qty * $modalUnit);
                        if ($qty > 0) $hasConfiguredModal = true;
                    } else {
                        // Fallback proporsional jika admin belum menginput modal (48% dari harga jual)
                        $totalStoreHpp += ($qty * round($prod['price'] * 0.48));
                    }
                }

                $pengeluaran = $totalStoreHpp;
                $laba = $pemasukan - $pengeluaran;
                $margin = $pemasukan > 0 ? round(($laba / $pemasukan) * 100, 1) : 0;

                $profitLossData->push((object) [
                    'store_name' => $sa['store_name'],
                    'periode' => $currentMonth,
                    'pemasukan' => $pemasukan,
                    'pengeluaran' => $pengeluaran,
                    'laba' => $laba,
                    'margin' => $margin,
                    'has_configured_modal' => $hasConfiguredModal,
                    'status' => $pemasukan > 0 ? ($laba >= 0 ? 'Surplus' : 'Defisit') : 'Aktif',
                ]);
            }
        }

        // Ringkasan Keuangan Global
        $totalFinancePemasukan = $profitLossData->sum('pemasukan');
        $totalFinancePengeluaran = $profitLossData->sum('pengeluaran');
        $totalFinanceLaba = $totalFinancePemasukan - $totalFinancePengeluaran;
        $totalFinanceMargin = $totalFinancePemasukan > 0 ? round(($totalFinanceLaba / $totalFinancePemasukan) * 100, 1) : 0;

        $financeSummary = [
            'total_pemasukan' => $totalFinancePemasukan,
            'total_pengeluaran' => $totalFinancePengeluaran,
            'total_laba' => $totalFinanceLaba,
            'average_margin' => $totalFinanceMargin,
        ];

        $hour = Carbon::now('Asia/Jakarta')->hour;
        if ($hour >= 4 && $hour < 11) {
            $greeting = 'Selamat Pagi';
        } elseif ($hour >= 11 && $hour < 15) {
            $greeting = 'Selamat Siang';
        } elseif ($hour >= 15 && $hour < 18) {
            $greeting = 'Selamat Sore';
        } else {
            $greeting = 'Selamat Malam';
        }

        $lowStockBatches = StockBatch::with(['store', 'coffeeType'])
            ->where('status', '!=', 'tarik')
            ->whereRaw('(jumlah_stock - laku) > 0 AND (jumlah_stock - laku) <= 3')
            ->orderByRaw('(jumlah_stock - laku) ASC')
            ->limit(6)
            ->get();

        return view('dashboard', compact(
            'totalToko', 'totalStock', 'totalLaku', 'expiringSoon',
            'totalRevenue', 'revenueGrowth',
            'topProducts', 'profitLossData', 'financeSummary',
            'greeting',
            'storeAnalytics', 'allStoreProductsAgg', 'storesList',
            'lowStockBatches'
        ));
    }
}
