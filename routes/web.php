<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Inventory\InventoryDashboardController;
use App\Http\Controllers\Inventory\WarehouseProductController;
use App\Http\Controllers\Inventory\StockMovementController;
use App\Http\Controllers\Inventory\SupplierController;
use App\Http\Controllers\Inventory\PurchaseOrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Canteen Dawn POS & Centralized Inventory Hub
|--------------------------------------------------------------------------
*/

// Authentication Routes (Guest Only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Logout Route (Authenticated)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Application Routes (Cashiers & Staff)
Route::middleware('auth')->group(function () {
    // Root Redirect
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // ==========================================
    // CANTEEN POS MODULE
    // ==========================================

    // 1. Dashboard (Chart.js CDN)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Point of Sale (Livewire 3)
    Route::get('/pos', function () {
        return view('pos');
    })->name('pos');

    // 3. Products Management
    Route::post('/products/{product}/toggle', [ProductController::class, 'toggleAvailability'])->name('products.toggle');
    Route::post('/products/{product}/restock', [ProductController::class, 'restock'])->name('products.restock');
    Route::resource('products', ProductController::class);

    // 4. Categories Management
    Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);

    // 5. Orders History
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // 6. Printable Receipt
    Route::get('/receipt/{order}', [ReceiptController::class, 'show'])->name('receipt.show');

    // 7. Sales Reports & CSV Export
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export');

    // ==========================================
    // INVENTORY MANAGEMENT MODULE (Group 2 Centralized)
    // ==========================================
    Route::prefix('inventory')->name('inventory.')->group(function () {
        // Inventory Dashboard
        Route::get('/', [InventoryDashboardController::class, 'index'])->name('dashboard');

        // Warehouse Products Catalog
        Route::resource('products', WarehouseProductController::class)->except(['show']);

        // Stock Movements Audit Ledger
        Route::get('/movements', [StockMovementController::class, 'index'])->name('movements.index');
        Route::post('/movements', [StockMovementController::class, 'store'])->name('movements.store');

        // Suppliers Directory
        Route::resource('suppliers', SupplierController::class)->except(['create', 'show', 'edit']);

        // Purchase Orders (Procurement)
        Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
        Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
        Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
        Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
        Route::post('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
        Route::post('/purchase-orders/{purchaseOrder}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');
    });
});
