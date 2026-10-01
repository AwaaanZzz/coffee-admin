<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CoffeeTypeController;
use App\Http\Controllers\StoreCoffeePriceController;
use App\Http\Controllers\StockBatchController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\FinanceReportController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\BarcodeScannerController;
use App\Http\Controllers\ActivityLogController;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Modal / HPP Kopi routes
    Route::get('/coffee-types/modal', [CoffeeTypeController::class, 'modalIndex'])->name('coffee-types.modal');
    Route::post('/coffee-types/modal', [CoffeeTypeController::class, 'modalUpdate'])->name('coffee-types.modal.update');
    
    // ALL existing routes (stores, coffee-types, stock, sales, finance) - copy from existing web.php
    Route::resource('stores', StoreController::class);
    Route::resource('coffee-types', CoffeeTypeController::class)->except('show');
    Route::get('/stores/{store}/prices', [StoreCoffeePriceController::class, 'edit'])->name('stores.prices.edit');
    Route::put('/stores/{store}/prices', [StoreCoffeePriceController::class, 'update'])->name('stores.prices.update');
    
    // Stock routes
    Route::get('/stock', [StockBatchController::class, 'index'])->name('stock.index');
    Route::get('/stock/create', [StockBatchController::class, 'create'])->name('stock.create');
    Route::get('/stock/generate-code', [StockBatchController::class, 'generateCode'])->name('stock.generate-code');
    Route::get('/stock/print-labels', [StockBatchController::class, 'printLabels'])->name('stock.print-labels');
    Route::post('/stock/regenerate-duplicates', [StockBatchController::class, 'regenerateDuplicates'])->name('stock.regenerate-duplicates');
    Route::post('/stock', [StockBatchController::class, 'store'])->name('stock.store');
    Route::get('/stock/{stock}/edit', [StockBatchController::class, 'edit'])->name('stock.edit');
    Route::put('/stock/{stock}', [StockBatchController::class, 'update'])->name('stock.update');
    Route::post('/stock/{stock}/tambah', [StockBatchController::class, 'tambahStock'])->name('stock.tambah');
    Route::delete('/stock/{stock}', [StockBatchController::class, 'destroy'])->name('stock.destroy');
    
    // Sales routes
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/available-stock/{store}', [SaleController::class, 'availableStock'])->name('sales.available-stock');
    Route::get('/sales/{sale}/invoice', [SaleController::class, 'invoice'])->name('sales.invoice');
    Route::get('/sales/{sale}/thermal', [SaleController::class, 'thermal'])->name('sales.thermal');
    Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');
    
    // Finance routes
    Route::get('/finance', [FinanceReportController::class, 'index'])->name('finance.index');
    Route::get('/finance/create', [FinanceReportController::class, 'create'])->name('finance.create');
    Route::post('/finance', [FinanceReportController::class, 'store'])->name('finance.store');
    Route::get('/finance/hitung-pemasukan', [FinanceReportController::class, 'hitungPemasukan'])->name('finance.hitung');
    Route::delete('/finance/{finance}', [FinanceReportController::class, 'destroy'])->name('finance.destroy');

    // Sales Reports & Analytics
    Route::get('/reports/sales', [SalesReportController::class, 'index'])->name('reports.sales');
    Route::get('/reports/sales/export/{format}', [SalesReportController::class, 'export'])->name('reports.sales.export');

    // Activity Log
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
    

    
    // NEW: Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    
    // NEW: Search
    Route::get('/search', [SearchController::class, 'search'])->name('search');
    
    // NEW: Todos
    Route::post('/todos', [TodoController::class, 'store'])->name('todos.store');
    Route::put('/todos/{todo}', [TodoController::class, 'update'])->name('todos.update');
    Route::delete('/todos/{todo}', [TodoController::class, 'destroy'])->name('todos.destroy');
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}/go', [NotificationController::class, 'go'])->name('notifications.go');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/clear-all', [NotificationController::class, 'clearAll'])->name('notifications.clear-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    

    
    // Stock Opname Mitra (Scanning Audit Lapangan)
    Route::get('/stock-opname', [StockOpnameController::class, 'index'])->name('stock-opname.index');
    Route::get('/stock-opname/{store}', [StockOpnameController::class, 'create'])->name('stock-opname.create');
    Route::post('/stock-opname/{store}', [StockOpnameController::class, 'submit'])->name('stock-opname.submit');
    Route::get('/stock-opname/receipt/{id}', [StockOpnameController::class, 'receipt'])->name('stock-opname.receipt');
    
    // Ekspor Laporan Komprehensif (Excel, CSV, PDF)
    Route::get('/exports', [ExportController::class, 'index'])->name('exports.index');
    Route::get('/export/sales/{format}', [ExportController::class, 'sales'])->name('export.sales');
    Route::get('/export/finance/{format}', [ExportController::class, 'finance'])->name('export.finance');
    Route::get('/export/stock/{format}', [ExportController::class, 'stock'])->name('export.stock');
    Route::get('/export/{type}/{format}', function ($type, $format, \Illuminate\Http\Request $request) {
        $controller = app(ExportController::class);
        if ($type === 'sales') return $controller->sales($request, $format);
        if ($type === 'finance') return $controller->finance($request, $format);
        if ($type === 'stock') return $controller->stock($request, $format);
        abort(404);
    })->name('export');

    // Terminal Barcode Scanner (Model T120)
    Route::get('/scanner', [BarcodeScannerController::class, 'index'])->name('scanner.index');
    Route::post('/scanner/lookup', [BarcodeScannerController::class, 'lookup'])->name('scanner.lookup');
    Route::post('/scanner/record-sale', [BarcodeScannerController::class, 'recordSale'])->name('scanner.record-sale');
});
