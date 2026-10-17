<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SaleController::class, 'recap'])->name('recap');
Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('sales', SaleController::class)->only(['index', 'store', 'destroy']);