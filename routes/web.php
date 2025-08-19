<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\Frontend\AuthenticationController;

Route::get('/login',  [AuthenticationController::class, 'login'])->name('login');

use App\Http\Controllers\Frontend\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/materials', [DashboardController::class, 'index'])->name('materials.index');
Route::get('/products',  [DashboardController::class, 'index'])->name('products.index');
Route::get('/suppliers', [DashboardController::class, 'index'])->name('suppliers.index');
Route::get('/customers', [DashboardController::class, 'index'])->name('customers.index');

// Pembelian
Route::prefix('pembelian')->name('pembelian.')->group(function () {
    Route::get('/material', [DashboardController::class, 'index'])->name('material.index');
});

// Manufactur/Produksi
Route::prefix('produksi')->name('produksi.')->group(function () {
    Route::get('/produk', [DashboardController::class, 'index'])->name('produk.index');
});

// Penjualan
use App\Http\Controllers\Frontend\Sales\ProductController;
Route::prefix('penjualan')->name('penjualan.')->group(function () {
    Route::get('/produk', [ProductController::class, 'index'])->name('produk.index');
    Route::get('/produk/buat', [ProductController::class, 'create'])->name('produk.create');

});
