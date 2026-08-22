<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\FinanceReport;
use App\Models\StockBatch;

class ExportController extends Controller
{
    public function export($type, $format)
    {
        if ($type === 'sales') {
            $data = Sale::with(['store', 'coffeeType'])->get();
            $view = 'exports.sales_print';
            $filename = 'sales_export_' . date('Ymd') . '.csv';
            $csvHeaders = ['ID', 'Store', 'Coffee Type', 'Jumlah', 'Total', 'Tanggal'];
            
            $csvCallback = function() use ($data) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['ID', 'Store', 'Coffee Type', 'Jumlah', 'Total', 'Tanggal']);
                foreach ($data as $row) {
                    fputcsv($file, [
                        $row->id, 
                        $row->store->name ?? '-', 
                        $row->coffeeType->name ?? '-', 
                        $row->jumlah, 
                        $row->total, 
                        $row->tanggal
                    ]);
                }
                fclose($file);
            };
        } elseif ($type === 'finance') {
            $data = FinanceReport::with('store')->get();
            $view = 'exports.finance_print';
            $filename = 'finance_export_' . date('Ymd') . '.csv';
            $csvHeaders = ['ID', 'Store', 'Periode', 'Pemasukan', 'Pengeluaran'];
            
            $csvCallback = function() use ($data) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['ID', 'Store', 'Periode', 'Pemasukan', 'Pengeluaran']);
                foreach ($data as $row) {
                    fputcsv($file, [
                        $row->id, 
                        $row->store->name ?? '-', 
                        $row->periode_mulai . ' - ' . $row->periode_selesai, 
                        $row->pemasukan, 
                        $row->pengeluaran
                    ]);
                }
                fclose($file);
            };
        } elseif ($type === 'stock') {
            $data = StockBatch::with('coffeeType')->get();
            $view = 'exports.stock_print';
            $filename = 'stock_export_' . date('Ymd') . '.csv';
            $csvHeaders = ['ID', 'Kode Produksi', 'Coffee Type', 'Jumlah Stock', 'Tgl Exp'];
            
            $csvCallback = function() use ($data) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['ID', 'Kode Produksi', 'Coffee Type', 'Jumlah Stock', 'Tgl Exp']);
                foreach ($data as $row) {
                    fputcsv($file, [
                        $row->id, 
                        $row->kode_produksi, 
                        $row->coffeeType->name ?? '-', 
                        $row->jumlah_stock, 
                        $row->tgl_exp
                    ]);
                }
                fclose($file);
            };
        } else {
            abort(404);
        }

        if ($format === 'csv') {
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];
            return response()->stream($csvCallback, 200, $headers);
        } elseif ($format === 'print') {
            return view($view, compact('data'));
        }

        return redirect()->back();
    }
}
