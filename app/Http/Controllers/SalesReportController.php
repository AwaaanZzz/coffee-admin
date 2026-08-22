<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Store;
use App\Models\CoffeeType;
use Carbon\Carbon;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['store', 'coffeeType']);

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }
        if ($request->filled('date_from')) {
            $query->where('tanggal', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('tanggal', '<=', $request->date_to);
        }
        if ($request->filled('coffee_type_id')) {
            $query->where('coffee_type_id', $request->coffee_type_id);
        }

        $sales = $query->get();

        $totalRevenue = $sales->sum('total');
        $totalUnits = $sales->sum('jumlah');
        $avgPerTransaction = $sales->count() > 0 ? $totalRevenue / $sales->count() : 0;

        $dailyBreakdown = $sales->groupBy('tanggal')->map(function ($row) {
            return $row->sum('total');
        });

        $topSellers = $sales->groupBy('coffee_type_id')->map(function ($row) {
            return $row->sum('jumlah');
        })->sortDesc()->take(5);

        $comparison = []; 

        $stores = Store::all();
        $coffeeTypes = CoffeeType::all();

        return view('reports.sales', compact(
            'sales', 'totalRevenue', 'totalUnits', 'avgPerTransaction',
            'dailyBreakdown', 'topSellers', 'comparison', 'stores', 'coffeeTypes'
        ));
    }

    public function export(Request $request, $format)
    {
        $query = Sale::with(['store', 'coffeeType']);
        
        if ($request->filled('store_id')) $query->where('store_id', $request->store_id);
        if ($request->filled('date_from')) $query->where('tanggal', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->where('tanggal', '<=', $request->date_to);
        if ($request->filled('coffee_type_id')) $query->where('coffee_type_id', $request->coffee_type_id);

        $sales = $query->get();

        if ($format === 'csv') {
            $fileName = 'sales_report_' . date('Ymd') . '.csv';
            $headers = array(
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );

            $columns = array('ID', 'Store', 'Coffee Type', 'Jumlah', 'Total', 'Tanggal');

            $callback = function() use($sales, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($sales as $sale) {
                    $row['ID']  = $sale->id;
                    $row['Store']    = $sale->store->name ?? '-';
                    $row['Coffee Type']  = $sale->coffeeType->name ?? '-';
                    $row['Jumlah']  = $sale->jumlah;
                    $row['Total']  = $sale->total;
                    $row['Tanggal']  = $sale->tanggal;

                    fputcsv($file, array($row['ID'], $row['Store'], $row['Coffee Type'], $row['Jumlah'], $row['Total'], $row['Tanggal']));
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } elseif ($format === 'pdf') {
            return view('reports.sales_print', compact('sales'));
        }

        return redirect()->back();
    }
}
