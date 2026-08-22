<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Sale;
use App\Models\FinanceReport;
use Illuminate\Http\Request;

class FinanceReportController extends Controller
{
    public function index(Request $request)
    {
        $query = FinanceReport::with('store');

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        $reports = $query->orderByDesc('periode_akhir')->paginate(15)->withQueryString();
        $stores = Store::orderBy('name')->get();

        return view('finance.index', compact('reports', 'stores'));
    }

    public function create()
    {
        $stores = Store::orderBy('name')->get();
        return view('finance.create', compact('stores'));
    }

    // Hitung otomatis pemasukan dari tabel sales sesuai toko & periode
    public function hitungPemasukan(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
        ]);

        $pemasukan = Sale::where('store_id', $validated['store_id'])
            ->whereBetween('tanggal', [$validated['periode_awal'], $validated['periode_akhir']])
            ->sum('total');

        return response()->json(['pemasukan' => $pemasukan]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
            'pemasukan' => 'required|numeric|min:0',
            'pengeluaran' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        $validated['laba'] = $validated['pemasukan'] - $validated['pengeluaran'];

        FinanceReport::create($validated);

        return redirect()->route('finance.index')->with('success', 'Laporan keuangan berhasil disimpan.');
    }

    public function destroy(FinanceReport $finance)
    {
        $finance->delete();
        return redirect()->route('finance.index')->with('success', 'Laporan dihapus.');
    }
}
