<?php

namespace App\Http\Controllers;

use App\Models\CoffeeType;
use App\Models\FinanceReport;
use App\Models\Sale;
use App\Models\StockBatch;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExportController extends Controller
{
    public function index()
    {
        $stores = Store::orderBy('name')->get();
        $totalSalesCount = Sale::count();
        $totalStockCount = StockBatch::where('status', '!=', 'tarik')->count();
        $totalCoffeeCount = CoffeeType::count();

        return view('exports.index', compact('stores', 'totalSalesCount', 'totalStockCount', 'totalCoffeeCount'));
    }

    public function sales(Request $request, $format = 'csv')
    {
        $query = Sale::with(['store', 'coffeeType', 'stockBatch']);

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $sales = $query->orderByDesc('tanggal')->orderByDesc('id')->get();
        $selectedStore = $request->filled('store_id') ? Store::find($request->store_id) : null;
        $startDate = $request->start_date ?? null;
        $endDate = $request->end_date ?? null;

        if ($format === 'json') {
            $rows = $sales->map(function ($s, $idx) {
                return [
                    'No' => $idx + 1,
                    'Tanggal' => $s->tanggal->format('d/m/Y'),
                    'Toko Mitra' => $s->store->name ?? '-',
                    'Varian Kopi' => $s->coffeeType->name ?? '-',
                    'Kode Batch' => $s->stockBatch->kode_produksi ?? '-',
                    'Jumlah (Pcs)' => (int) $s->jumlah,
                    'Harga Satuan (Rp)' => (float) $s->harga,
                    'Total Penjualan (Rp)' => (float) $s->total,
                ];
            });

            return response()->json([
                'title' => 'Laporan_Penjualan_Kopi_Hiku_Himu',
                'store' => $selectedStore?->name ?? 'Semua Toko',
                'periode' => ($startDate && $endDate) ? "{$startDate} sd {$endDate}" : 'Semua Periode',
                'summary' => [
                    'total_transaksi' => $sales->count(),
                    'total_pcs' => $sales->sum('jumlah'),
                    'total_nominal' => $sales->sum('total'),
                ],
                'data' => $rows
            ]);
        }

        if ($format === 'print') {
            return view('exports.sales_pdf', compact('sales', 'selectedStore', 'startDate', 'endDate'));
        }

        // CSV Stream fallback
        $filename = 'laporan_penjualan_' . date('Ymd_His') . '.csv';
        return response()->stream(function () use ($sales) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['No', 'Tanggal', 'Toko Mitra', 'Varian Kopi', 'Kode Batch', 'Jumlah (Pcs)', 'Harga Satuan (Rp)', 'Total (Rp)'], ';');

            foreach ($sales as $idx => $s) {
                fputcsv($handle, [
                    $idx + 1,
                    $s->tanggal->format('d/m/Y'),
                    $s->store->name ?? '-',
                    $s->coffeeType->name ?? '-',
                    $s->stockBatch->kode_produksi ?? '-',
                    $s->jumlah,
                    $s->harga,
                    $s->total,
                ], ';');
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function finance(Request $request, $format = 'csv')
    {
        $coffeeTypes = CoffeeType::with(['storePrices'])->orderBy('category')->orderBy('name')->get();

        // Query sales aggregations
        $salesQuery = Sale::selectRaw('coffee_type_id, SUM(jumlah) as total_qty, SUM(total) as total_revenue, AVG(harga) as avg_sale_price')
            ->groupBy('coffee_type_id');

        if ($request->filled('store_id')) {
            $salesQuery->where('store_id', $request->store_id);
        }
        if ($request->filled('start_date')) {
            $salesQuery->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $salesQuery->whereDate('tanggal', '<=', $request->end_date);
        }

        $salesAgg = $salesQuery->get()->keyBy('coffee_type_id');

        $selectedStore = $request->filled('store_id') ? Store::find($request->store_id) : null;
        $startDate = $request->start_date ?? null;
        $endDate = $request->end_date ?? null;

        $reportItems = $coffeeTypes->map(function ($c) use ($salesAgg) {
            $agg = $salesAgg->get($c->id);
            $qty = (int) ($agg?->total_qty ?? 0);
            $revenue = (float) ($agg?->total_revenue ?? 0);
            $avgPrice = $qty > 0 ? ($revenue / $qty) : (float) ($c->storePrices->avg('price') ?? 0);
            $modal = (float) ($c->modal ?? 0);
            $totalHpp = $qty * $modal;
            $labaKotor = $revenue - $totalHpp;
            $marginPct = $revenue > 0 ? ($labaKotor / $revenue) * 100 : 0;

            return [
                'id' => $c->id,
                'name' => $c->name,
                'category' => strtoupper($c->category ?? 'ROBUSTA'),
                'total_qty' => $qty,
                'avg_price' => round($avgPrice),
                'revenue' => $revenue,
                'modal' => $modal,
                'total_hpp' => $totalHpp,
                'laba_kotor' => $labaKotor,
                'margin_pct' => round($marginPct, 1),
            ];
        });

        $totalRevenue = $reportItems->sum('revenue');
        $totalHpp = $reportItems->sum('total_hpp');
        $totalLaba = $totalRevenue - $totalHpp;
        $overallMargin = $totalRevenue > 0 ? ($totalLaba / $totalRevenue) * 100 : 0;

        if ($format === 'json') {
            $rows = $reportItems->map(function ($item, $idx) {
                return [
                    'No' => $idx + 1,
                    'Varian Kopi' => $item['name'],
                    'Kategori' => $item['category'],
                    'Total Terjual (Pcs)' => $item['total_qty'],
                    'Rata-rata Harga Jual (Rp)' => $item['avg_price'],
                    'Total Omset (Rp)' => $item['revenue'],
                    'Modal HPP per Unit (Rp)' => $item['modal'],
                    'Beban Pokok HPP (Rp)' => $item['total_hpp'],
                    'Laba Kotor (Rp)' => $item['laba_kotor'],
                    'Margin (%)' => $item['margin_pct'] . '%',
                ];
            });

            return response()->json([
                'title' => 'Laporan_Laba_Rugi_Profit_Loss_Kopi_Hiku_Himu',
                'store' => $selectedStore?->name ?? 'Semua Toko',
                'periode' => ($startDate && $endDate) ? "{$startDate} sd {$endDate}" : 'Semua Periode',
                'summary' => [
                    'total_omset' => $totalRevenue,
                    'total_hpp' => $totalHpp,
                    'total_laba' => $totalLaba,
                    'margin_keseluruhan' => round($overallMargin, 1) . '%',
                ],
                'data' => $rows
            ]);
        }

        if ($format === 'print') {
            return view('exports.finance_pdf', compact('reportItems', 'selectedStore', 'startDate', 'endDate', 'totalRevenue', 'totalHpp', 'totalLaba', 'overallMargin'));
        }

        // CSV Stream
        $filename = 'laporan_laba_rugi_' . date('Ymd_His') . '.csv';
        return response()->stream(function () use ($reportItems, $totalRevenue, $totalHpp, $totalLaba, $overallMargin) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['No', 'Varian Kopi', 'Kategori', 'Total Terjual (Pcs)', 'Rata-rata Harga (Rp)', 'Total Omset (Rp)', 'Modal HPP (Rp)', 'Total HPP (Rp)', 'Laba Kotor (Rp)', 'Margin (%)'], ';');

            foreach ($reportItems as $idx => $it) {
                fputcsv($handle, [
                    $idx + 1,
                    $it['name'],
                    $it['category'],
                    $it['total_qty'],
                    $it['avg_price'],
                    $it['revenue'],
                    $it['modal'],
                    $it['total_hpp'],
                    $it['laba_kotor'],
                    $it['margin_pct'] . '%',
                ], ';');
            }

            fputcsv($handle, ['TOTAL KESELURUHAN', '', '', '', '', $totalRevenue, '', $totalHpp, $totalLaba, round($overallMargin, 1) . '%'], ';');
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function stock(Request $request, $format = 'csv')
    {
        $query = StockBatch::with(['store', 'coffeeType'])->where('status', '!=', 'tarik');

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'expiring') {
                $query->whereBetween('tgl_exp', [now()->toDateString(), now()->addDays(7)->toDateString()]);
            } elseif ($request->status === 'expired') {
                $query->where('tgl_exp', '<', now()->toDateString());
            } elseif ($request->status === 'active') {
                $query->where('tgl_exp', '>=', now()->toDateString())->whereRaw('(jumlah_stock - laku) > 0');
            }
        }

        $batches = $query->orderBy('store_id')->orderBy('kode_produksi')->get();
        $selectedStore = $request->filled('store_id') ? Store::find($request->store_id) : null;

        // Map items with prices & asset values
        $stockItems = $batches->map(function ($b, $idx) {
            $price = (float) ($b->store->coffeePrices->firstWhere('coffee_type_id', $b->coffee_type_id)?->price ?? 0);
            $modal = (float) ($b->coffeeType->modal ?? 0);
            $sisa = $b->sisa;
            $nilaiJual = $sisa * $price;
            $nilaiHpp = $sisa * $modal;

            return [
                'no' => $idx + 1,
                'kode_produksi' => $b->kode_produksi,
                'store_name' => $b->store->name ?? '-',
                'coffee_name' => $b->coffeeType->name ?? '-',
                'category' => strtoupper($b->coffeeType->category ?? 'ROBUSTA'),
                'jumlah_stock' => $b->jumlah_stock,
                'laku' => $b->laku,
                'sisa' => $sisa,
                'harga_jual' => $price,
                'modal' => $modal,
                'nilai_aset' => $nilaiJual,
                'tgl_stock' => $b->tgl_stock->format('d/m/Y'),
                'tgl_exp' => $b->tgl_exp->format('d/m/Y'),
                'is_expiring' => $b->is_expiring_soon,
                'is_expired' => $b->is_expired,
            ];
        });

        if ($format === 'json') {
            $rows = $stockItems->map(function ($item) {
                return [
                    'No' => $item['no'],
                    'Kode Batch' => $item['kode_produksi'],
                    'Toko Mitra' => $item['store_name'],
                    'Varian Kopi' => $item['coffee_name'],
                    'Kategori' => $item['category'],
                    'Stok Masuk (Pcs)' => $item['jumlah_stock'],
                    'Terjual (Pcs)' => $item['laku'],
                    'Sisa Fisik (Pcs)' => $item['sisa'],
                    'Harga Konsinyasi (Rp)' => $item['harga_jual'],
                    'Total Nilai Sisa (Rp)' => $item['nilai_aset'],
                    'Tgl Masuk' => $item['tgl_stock'],
                    'Tgl Expired' => $item['tgl_exp'],
                    'Status' => $item['is_expired'] ? 'EXPIRED' : ($item['is_expiring'] ? 'HAMPIR EXP' : 'AMAN'),
                ];
            });

            return response()->json([
                'title' => 'Laporan_Inventaris_Stok_Kopi_Hiku_Himu',
                'store' => $selectedStore?->name ?? 'Semua Toko',
                'summary' => [
                    'total_batch' => $stockItems->count(),
                    'total_sisa_pcs' => $stockItems->sum('sisa'),
                    'total_nilai_aset' => $stockItems->sum('nilai_aset'),
                ],
                'data' => $rows
            ]);
        }

        if ($format === 'print') {
            return view('exports.stock_pdf', compact('stockItems', 'selectedStore'));
        }

        // CSV Stream
        $filename = 'laporan_stok_kopi_' . date('Ymd_His') . '.csv';
        return response()->stream(function () use ($stockItems) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['No', 'Kode Batch', 'Toko Mitra', 'Varian Kopi', 'Kategori', 'Stok Masuk (Pcs)', 'Terjual (Pcs)', 'Sisa (Pcs)', 'Harga Satuan (Rp)', 'Nilai Aset (Rp)', 'Tgl Masuk', 'Tgl Exp', 'Status'], ';');

            foreach ($stockItems as $it) {
                fputcsv($handle, [
                    $it['no'],
                    $it['kode_produksi'],
                    $it['store_name'],
                    $it['coffee_name'],
                    $it['category'],
                    $it['jumlah_stock'],
                    $it['laku'],
                    $it['sisa'],
                    $it['harga_jual'],
                    $it['nilai_aset'],
                    $it['tgl_stock'],
                    $it['tgl_exp'],
                    $it['is_expired'] ? 'EXPIRED' : ($it['is_expiring'] ? 'HAMPIR EXP' : 'AMAN'),
                ], ';');
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
