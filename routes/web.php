<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SaleController::class, 'recap'])->name('recap');
Route::resource('categories', CategoryController::class);

// Riwayat hapus HARUS dideklarasi SEBELUM Route::resource('sales') agar
// tidak tertangkap oleh sales/{sale} parameter binding
Route::get('/sales/trash', [SaleController::class, 'trash'])->name('sales.trash');

Route::resource('sales', SaleController::class);