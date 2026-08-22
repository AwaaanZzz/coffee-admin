<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockBatch;
use App\Models\Sale;
use App\Models\FinanceReport;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar.index');
    }

    public function events(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $events = [];

        $stocks = StockBatch::whereMonth('tgl_exp', $month)->whereYear('tgl_exp', $year)->get();
        foreach ($stocks as $stock) {
            $events[] = [
                'title' => 'Stock Exp: ' . $stock->kode_produksi,
                'date' => Carbon::parse($stock->tgl_exp)->format('Y-m-d'),
                'type' => 'danger',
                'description' => 'Stock ini akan kadaluarsa.'
            ];
        }

        $sales = Sale::whereMonth('tanggal', $month)->whereYear('tanggal', $year)->get();
        foreach ($sales as $sale) {
            $events[] = [
                'title' => 'Penjualan: Rp ' . number_format($sale->total, 0, ',', '.'),
                'date' => Carbon::parse($sale->tanggal)->format('Y-m-d'),
                'type' => 'info',
                'description' => 'Transaksi penjualan pada ' . $sale->tanggal
            ];
        }

        $reports = FinanceReport::whereMonth('periode_mulai', $month)->whereYear('periode_mulai', $year)->get();
        foreach ($reports as $report) {
            $events[] = [
                'title' => 'Laporan Keuangan',
                'date' => Carbon::parse($report->periode_mulai)->format('Y-m-d'),
                'type' => 'success',
                'description' => 'Awal periode laporan keuangan.'
            ];
        }

        return response()->json($events);
    }
}
