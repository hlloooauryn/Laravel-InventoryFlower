<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard & Redirect root
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Transactions / Stock Movements (Staff & Admin can operate)
    Route::get('/transactions/history', [TransactionController::class, 'history'])->name('transactions.history');
    Route::get('/transactions/stock-in', [TransactionController::class, 'showStockIn'])->name('transactions.stock_in');
    Route::post('/transactions/stock-in', [TransactionController::class, 'processStockIn']);
    Route::get('/transactions/stock-out', [TransactionController::class, 'showStockOut'])->name('transactions.stock_out');
    Route::post('/transactions/stock-out', [TransactionController::class, 'processStockOut']);

    // Products (Staff can view/search/filter; mutations require admin)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show')->whereNumber('product');

    // Categories (Staff can view; mutations require admin)
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

    // Admin Only Routes
    Route::middleware('admin')->group(function () {
        // Products CRUD actions
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Categories CRUD actions
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Suppliers CRUD
        Route::resource('suppliers', SupplierController::class);

        // Users Management CRUD
        Route::resource('users', UserController::class)->except(['show']);
    });
});
