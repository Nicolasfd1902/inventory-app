<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ItemController;
use App\Http\Controllers\StockMutationController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('inventory.index');
});

// Endpoint untuk barang & pencarian
Route::get('/items/data', [ItemController::class, 'getData']); // Fetch data + search
Route::resource('items', ItemController::class)->except(['create', 'edit']);

// Endpoint transaksi & log mutasi
Route::post('/stock-mutations', [StockMutationController::class, 'store']);
Route::get('/stock-mutations/data', [StockMutationController::class, 'getData']);
