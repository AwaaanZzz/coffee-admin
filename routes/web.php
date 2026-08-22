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

use App\Http\Controllers\ExportController;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // ALL existing routes (stores, coffee-types, stock, sales, finance) - copy from existing web.php
    Route::resource('stores', StoreController::class);
    Route::resource('coffee-types', CoffeeTypeController::class)->except('show');
    Route::get('/stores/{store}/prices', [StoreCoffeePriceController::class, 'edit'])->name('stores.prices.edit');
    Route::put('/stores/{store}/prices', [StoreCoffeePriceController::class, 'update'])->name('stores.prices.update');
    
    // Stock routes
    Route::get('/stock', [StockBatchController::class, 'index'])->name('stock.index');
    Route::get('/stock/create', [StockBatchController::class, 'create'])->name('stock.create');
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
    Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');
    

    
    // NEW: Calendar
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
    
    // NEW: Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    

    
    // NEW: Export
    Route::get('/export/{type}/{format}', [ExportController::class, 'export'])->name('export');
});
