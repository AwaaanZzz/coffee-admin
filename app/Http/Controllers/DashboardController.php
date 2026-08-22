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

        $salesChart = Sale::selectRaw('tanggal, SUM(total) as total')
            ->where('tanggal', '>=', Carbon::now()->subDays(29))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $chartLabels = $salesChart->pluck('tanggal')->map(fn ($d) => Carbon::parse($d)->format('d M'));
        $chartValues = $salesChart->pluck('total');

        $topProducts = Sale::selectRaw('coffee_type_id, SUM(jumlah) as total_qty')
            ->groupBy('coffee_type_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->with('coffeeType')
            ->get();

        $salesByStore = Sale::selectRaw('store_id, SUM(total) as total_sales')
            ->groupBy('store_id')
            ->orderByDesc('total_sales')
            ->with('store')
            ->get();

        $profitLossData = FinanceReport::with('store')->latest()->limit(5)->get()->map(function ($report) {
            $laba = $report->pemasukan - $report->pengeluaran;
            $margin = $report->pemasukan > 0 ? ($laba / $report->pemasukan) * 100 : 0;
            $report->laba = $laba;
            $report->margin = $margin;
            return $report;
        });

        $recentSales = Sale::with(['store', 'coffeeType'])->latest('tanggal')->limit(10)->get();

        $todos = Auth::check() && class_exists(Todo::class) ? Todo::where('user_id', Auth::id())->get() : collect();

        $activeBatches = StockBatch::where('jumlah_stock', '>', 0)->get();
        $stockForecasting = $activeBatches->map(function ($batch) {
            $daysSinceCreated = Carbon::parse($batch->created_at)->diffInDays(now()) ?: 1;
            $avgDailySales = $batch->laku / $daysSinceCreated;
            $daysUntilEmpty = $avgDailySales > 0 ? $batch->jumlah_stock / $avgDailySales : 999;
            
            $batch->avg_daily_sales = $avgDailySales;
            $batch->days_until_empty = round($daysUntilEmpty);
            return $batch;
        });

        $hour = now()->hour;
        if ($hour < 11) {
            $greeting = 'Selamat Pagi';
        } elseif ($hour < 15) {
            $greeting = 'Selamat Siang';
        } elseif ($hour < 18) {
            $greeting = 'Selamat Sore';
        } else {
            $greeting = 'Selamat Malam';
        }

        return view('dashboard', compact(
            'totalToko', 'totalStock', 'totalLaku', 'expiringSoon',
            'chartLabels', 'chartValues', 'totalRevenue', 'revenueGrowth',
            'topProducts', 'salesByStore', 'profitLossData', 'recentSales',
            'todos', 'stockForecasting', 'greeting'
        ));
    }
}
