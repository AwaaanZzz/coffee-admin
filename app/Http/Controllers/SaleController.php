<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StockBatch;
use App\Models\Sale;
use App\Models\StockLog;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['store', 'coffeeType']);

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        $sales = $query->orderByDesc('tanggal')->orderByDesc('id')->paginate(20)->withQueryString();
        $stores = Store::orderBy('name')->get();

        $sameDayCounts = [];
        if ($sales->isNotEmpty()) {
            $storeIds = $sales->pluck('store_id')->unique();
            $dates = $sales->pluck('tanggal')->map(fn($d) => $d->format('Y-m-d'))->unique();

            $sameDayCounts = Sale::whereIn('store_id', $storeIds)
                ->whereIn(Sale::raw('DATE(tanggal)'), $dates)
                ->selectRaw('store_id, DATE(tanggal) as tgl, COUNT(*) as total')
                ->groupBy('store_id', Sale::raw('DATE(tanggal)'))
                ->get()
                ->mapWithKeys(fn($item) => [$item->store_id . '_' . $item->tgl => $item->total])
                ->all();
        }

        return view('sales.index', compact('sales', 'stores', 'sameDayCounts'));
    }

    public function create()
    {
        $stores = Store::orderBy('name')->get();
        return view('sales.create', compact('stores'));
    }

    // Dipanggil via AJAX/JS: ambil batch stock yang tersedia (sisa > 0) untuk toko tertentu
    public function availableStock(Store $store)
    {
        $batches = StockBatch::with('coffeeType')
            ->where('store_id', $store->id)
            ->where('status', '!=', 'tarik')
            ->get()
            ->filter(fn ($b) => $b->sisa > 0)
            ->map(function ($b) {
                $price = $b->store->coffeePrices->firstWhere('coffee_type_id', $b->coffee_type_id)?->price ?? 0;
                return [
                    'id' => $b->id,
                    'label' => "{$b->coffeeType->name} (Sisa: {$b->sisa}, Kode: {$b->kode_produksi})",
                    'sisa' => $b->sisa,
                    'price' => $price,
                    'coffee_type_id' => $b->coffee_type_id,
                ];
            })->values();

        return response()->json($batches);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'stock_batch_id' => 'required|exists:stock_batches,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal' => 'required|date',
        ]);

        $batch = StockBatch::findOrFail($validated['stock_batch_id']);

        if ($validated['jumlah'] > $batch->sisa) {
            return back()->withErrors(['jumlah' => 'Jumlah melebihi sisa stock (' . $batch->sisa . ').'])->withInput();
        }

        $price = $batch->store->coffeePrices->firstWhere('coffee_type_id', $batch->coffee_type_id)?->price ?? 0;

        Sale::create([
            'store_id' => $batch->store_id,
            'coffee_type_id' => $batch->coffee_type_id,
            'stock_batch_id' => $batch->id,
            'jumlah' => $validated['jumlah'],
            'harga' => $price,
            'total' => $price * $validated['jumlah'],
            'tanggal' => $validated['tanggal'],
        ]);

        $batch->increment('laku', $validated['jumlah']);

        StockLog::create([
            'stock_batch_id' => $batch->id,
            'type' => 'update',
            'jumlah' => $validated['jumlah'],
            'keterangan' => 'Penjualan tanggal ' . $validated['tanggal'],
        ]);

        return redirect()->route('sales.index')->with('success', 'Penjualan berhasil dicatat.');
    }

    public function destroy(Sale $sale)
    {
        // Kembalikan laku di stock batch
        if ($sale->stockBatch) {
            $sale->stockBatch->decrement('laku', min($sale->jumlah, $sale->stockBatch->laku));
        }
        $sale->delete();

        return redirect()->route('sales.index')->with('success', 'Data penjualan dihapus & stock dikembalikan.');
    }

    public function invoice(Request $request, Sale $sale)
    {
        $sale->load(['store', 'coffeeType', 'stockBatch']);

        $isBatch = $request->query('mode') === 'batch';

        if ($isBatch) {
            $items = Sale::with(['coffeeType', 'stockBatch'])
                ->where('store_id', $sale->store_id)
                ->whereDate('tanggal', $sale->tanggal)
                ->orderBy('id')
                ->get();
        } else {
            $items = collect([$sale]);
        }

        $storeSameDaySalesCount = Sale::where('store_id', $sale->store_id)
            ->whereDate('tanggal', $sale->tanggal)
            ->count();

        $grandTotal = $items->sum('total');
        $totalQty = $items->sum('jumlah');
        $rawTerbilang = trim(preg_replace('/\s+/', ' ', $this->terbilang((int) $grandTotal)));
        $terbilang = $rawTerbilang ? ($rawTerbilang . ' Rupiah') : 'Nol Rupiah';

        $invoiceNumber = 'INV/' . $sale->tanggal->format('Ymd') . '/' . str_pad($sale->id, 5, '0', STR_PAD_LEFT);
        $whatsAppText = $this->generateWhatsAppText($sale, $items, $grandTotal, $invoiceNumber, $isBatch);
        $whatsAppUrl = 'https://api.whatsapp.com/send?text=' . urlencode($whatsAppText);

        return view('sales.invoice', compact(
            'sale',
            'items',
            'isBatch',
            'storeSameDaySalesCount',
            'grandTotal',
            'totalQty',
            'terbilang',
            'invoiceNumber',
            'whatsAppText',
            'whatsAppUrl'
        ));
    }

    public function thermal(Request $request, Sale $sale)
    {
        $sale->load(['store', 'coffeeType', 'stockBatch']);

        $isBatch = $request->query('mode') === 'batch';

        if ($isBatch) {
            $items = Sale::with(['coffeeType', 'stockBatch'])
                ->where('store_id', $sale->store_id)
                ->whereDate('tanggal', $sale->tanggal)
                ->orderBy('id')
                ->get();
        } else {
            $items = collect([$sale]);
        }

        $storeSameDaySalesCount = Sale::where('store_id', $sale->store_id)
            ->whereDate('tanggal', $sale->tanggal)
            ->count();

        $grandTotal = $items->sum('total');
        $totalQty = $items->sum('jumlah');
        $invoiceNumber = 'TRX/' . $sale->tanggal->format('ymd') . '/' . str_pad($sale->id, 4, '0', STR_PAD_LEFT);
        $whatsAppText = $this->generateWhatsAppText($sale, $items, $grandTotal, $invoiceNumber, $isBatch);
        $whatsAppUrl = 'https://api.whatsapp.com/send?text=' . urlencode($whatsAppText);

        return view('sales.thermal', compact(
            'sale',
            'items',
            'isBatch',
            'storeSameDaySalesCount',
            'grandTotal',
            'totalQty',
            'invoiceNumber',
            'whatsAppText',
            'whatsAppUrl'
        ));
    }

    public function generateWhatsAppText($sale, $items, $grandTotal, $invoiceNumber, $isBatch = false)
    {
        $storeName = $sale->store?->name ?? 'Pelanggan';
        $tgl = $sale->tanggal ? $sale->tanggal->format('d/m/Y') : now()->format('d/m/Y');
        $jam = now()->format('H:i');
        $kasir = auth()->check() ? auth()->user()->name : 'Admin';

        $msg = "*" . strtoupper(config('business.name', 'KOPI HIKU HIMU')) . "*\n";
        $msg .= "_" . config('business.tagline', 'Roastery & Distribusi Kopi') . "_\n";
        $msg .= config('business.address') . "\n";
        $msg .= "HP / WA: " . config('business.phone', '0812-1287-8844') . "\n\n";

        $msg .= "=======================\n";
        $msg .= "*NOTA ELEKTRONIK*\n";
        $msg .= "=======================\n";
        $msg .= "No Nota     : " . $invoiceNumber . "\n";
        $msg .= "Mitra/Toko  : " . $storeName . "\n";
        if (!empty($sale->store?->penanggung_jawab)) {
            $msg .= "Kontak (PJ) : " . $sale->store->penanggung_jawab . "\n";
        }
        $msg .= "Tanggal     : " . $tgl . " - " . $jam . " WIB\n";
        $msg .= "Kasir/Petugas: " . $kasir . "\n\n";

        $msg .= "=======================\n";
        $msg .= "*RINCIAN PESANAN*\n";
        $msg .= "=======================\n";

        foreach ($items as $item) {
            $namaKopi = $item->coffeeType?->name ?? 'Kopi';
            $qty = $item->jumlah;
            $harga = number_format($item->harga, 0, ',', '.');
            $subtotal = number_format($item->total, 0, ',', '.');
            $batchStr = $item->stockBatch?->kode_produksi ? " (" . $item->stockBatch->kode_produksi . ")" : "";

            $msg .= "- " . $namaKopi . $batchStr . "\n";
            $msg .= "  " . $qty . " Pcs x Rp " . $harga . " = Rp " . $subtotal . "\n\n";
        }

        $totalFmt = number_format($grandTotal, 0, ',', '.');
        $msg .= "=======================\n";
        $msg .= "Status    : *LUNAS / TERCATAT* \u{2705}\n";
        $msg .= "=======================\n";
        $msg .= "subTotal  =  Rp " . $totalFmt . "\n";
        $msg .= "Diskon    =  Rp 0\n";
        $msg .= "Total     =  *Rp " . $totalFmt . "*\n";
        $msg .= "=======================\n\n";

        $msg .= "PERHATIAN!! \u{1F4CC}\n";
        $msg .= "1. Pengambilan / verifikasi fisik wajib menunjukkan nota cetak atau nota WA ini.\n";
        $msg .= "2. Komplain kualitas / selisih barang kami layani maksimal 1x24 jam sejak barang diterima dengan melampirkan fisik produk utuh.\n";
        $msg .= "3. Apabila mitra/konsumen tidak menghitung jumlah fisik saat serah terima, maka jumlah yang kami catat dianggap benar.\n";
        $msg .= "4. Garansi penggantian berlaku jika ditemukan cacat segel kemasan atau roasting dari pihak kami.\n\n";

        $msg .= "KAMI TIDAK BERTANGGUNG JAWAB ATAS:\n";
        $msg .= "1. Penurunan aroma / rasa akibat penyimpanan di tempat lembap atau terkena sinar matahari langsung setelah diterima.\n";
        $msg .= "2. Produk yang kemasannya telah dibuka, digiling ulang sendiri, atau dipindahtangankan tanpa persetujuan.\n";
        $msg .= "3. Kerusakan fisik akibat bencana alam / Force Majeure.\n\n";

        $msg .= "TERIMAKASIH atas kerja sama dan kepercayaannya! \u{2615}\u{2728}\n\n";
        $msg .= "Tautan E-Nota Resmi:\n";
        $msg .= route('sales.invoice', $sale->id) . ($isBatch ? '?mode=batch' : '') . "\n\n";
        $msg .= "Terima Kasih";

        return $msg;
    }

    private function terbilang($number)
    {
        $bilangan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $number = abs($number);

        if ($number < 12) {
            return $bilangan[$number];
        } elseif ($number < 20) {
            return $this->terbilang($number - 10) . ' Belas';
        } elseif ($number < 100) {
            return $this->terbilang(intdiv($number, 10)) . ' Puluh ' . $this->terbilang($number % 10);
        } elseif ($number < 200) {
            return 'Seratus ' . $this->terbilang($number - 100);
        } elseif ($number < 1000) {
            return $this->terbilang(intdiv($number, 100)) . ' Ratus ' . $this->terbilang($number % 100);
        } elseif ($number < 2000) {
            return 'Seribu ' . $this->terbilang($number - 1000);
        } elseif ($number < 1000000) {
            return $this->terbilang(intdiv($number, 1000)) . ' Ribu ' . $this->terbilang($number % 1000);
        } elseif ($number < 1000000000) {
            return $this->terbilang(intdiv($number, 1000000)) . ' Juta ' . $this->terbilang($number % 1000000);
        } elseif ($number < 1000000000000) {
            return $this->terbilang(intdiv($number, 1000000000)) . ' Miliar ' . $this->terbilang($number % 1000000000);
        }

        return '';
    }
}
