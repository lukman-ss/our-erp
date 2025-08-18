<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\AuthenticationController;

Route::post('/auth/register', [AuthenticationController::class, 'register']);
Route::post('/auth/login', [AuthenticationController::class, 'do_login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/auth/me', [AuthenticationController::class, 'me']);
    Route::post('/auth/refresh', [AuthenticationController::class, 'refresh']);
    Route::post('/auth/logout', [AuthenticationController::class, 'logout']);
});

use App\Http\Controllers\Backend\MaterialController;

Route::middleware('auth:api')->group(function () {
    Route::get('/materials/datatable', [MaterialController::class, 'datatable']);
    Route::get('/materials/{id}', [MaterialController::class, 'show']);
    Route::post('/materials', [MaterialController::class, 'store']);
    Route::put('/materials/{id}', [MaterialController::class, 'update']);
    Route::delete('/materials/{id}', [MaterialController::class, 'destroy']);
});

use App\Http\Controllers\Backend\ProductController;

Route::middleware('auth:api')->group(function () {
    Route::get('/products/datatable', [ProductController::class, 'datatable']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::post('/products', [ProductController::class, 'store']);      // create product + BOM
    Route::put('/products/{id}', [ProductController::class, 'update']); // update product (+ re-sync BOM if items sent)
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});

use App\Http\Controllers\Backend\SupplierController;

Route::middleware('auth:api')->group(function () {
    Route::get('/suppliers/datatable', [SupplierController::class, 'datatable']);
    Route::get('/suppliers/{id}', [SupplierController::class, 'show']);
    Route::post('/suppliers', [SupplierController::class, 'store']);     // create supplier
    Route::put('/suppliers/{id}', [SupplierController::class, 'update']); // update supplier
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy']); // delete supplier
});


use App\Http\Controllers\Backend\PurchaseController;

Route::middleware('auth:api')->group(function () {
    Route::get('/purchases/datatable',  [PurchaseController::class, 'datatables']);
    Route::get('/purchases',            [PurchaseController::class, 'index']);
    Route::get('/purchases/{id}',       [PurchaseController::class, 'show']);
    Route::post('/purchases',           [PurchaseController::class, 'store']);
    Route::put('/purchases/{id}',       [PurchaseController::class, 'update']);
    Route::delete('/purchases/{id}',    [PurchaseController::class, 'destroy']);
});

use App\Http\Controllers\Backend\ProductionController;

Route::middleware('auth:api')->group(function () {
    Route::get('/productions/datatable', [ProductionController::class, 'datatable']); // kalau butuh DataTables
    Route::get('/productions',           [ProductionController::class, 'index']);     // list all
    Route::get('/productions/{id}',      [ProductionController::class, 'show']);      // detail
    Route::post('/productions',          [ProductionController::class, 'store']);     // create
    Route::put('/productions/{id}',      [ProductionController::class, 'update']);    // update
    Route::delete('/productions/{id}',   [ProductionController::class, 'destroy']);   // delete
    Route::post('/productions/{id}/start',  [ProductionController::class, 'start']);   // mark as in_progress
    Route::post('/productions/{id}/finish', [ProductionController::class, 'finish']);  // mark as finished
});
