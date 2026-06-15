<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BarangApiController;
use App\Http\Controllers\Api\LokasiApiController;
use App\Http\Controllers\Api\TransaksiApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    // ─── BARANG ───
    Route::get('/barang',             [BarangApiController::class, 'index'])->name('api.barang.index');
    Route::get('/barang/{id}',        [BarangApiController::class, 'show'])->name('api.barang.show');
    Route::get('/barang/{id}/items',  [BarangApiController::class, 'items'])->name('api.barang.items');
    Route::get('/barang/{id}/stock',  [BarangApiController::class, 'stock'])->name('api.barang.stock');
    Route::get('/barang/{id}/transaksi', [BarangApiController::class, 'transaksi'])->name('api.barang.transaksi');

    // ─── LOKASI ───
    Route::get('/lokasi',          [LokasiApiController::class, 'index'])->name('api.lokasi.index');
    Route::get('/lokasi/{id}/stock', [LokasiApiController::class, 'stock'])->name('api.lokasi.stock');

    // ─── TRANSAKSI ───
    Route::post('/transaksi/masuk',           [TransaksiApiController::class, 'masuk'])->name('api.transaksi.masuk');
    Route::post('/transaksi/keluar',          [TransaksiApiController::class, 'keluar'])->name('api.transaksi.keluar');
    Route::post('/transaksi/pindah',          [TransaksiApiController::class, 'pindah'])->name('api.transaksi.pindah');
    Route::post('/transaksi/update-kondisi',  [TransaksiApiController::class, 'updateKondisi'])->name('api.transaksi.update-kondisi');
    Route::get('/transaksi',                  [TransaksiApiController::class, 'index'])->name('api.transaksi.index');
    Route::get('/transaksi/{id}',             [TransaksiApiController::class, 'show'])->name('api.transaksi.show');
    Route::delete('/transaksi/{id}',          [TransaksiApiController::class, 'destroy'])->name('api.transaksi.destroy');
    Route::get('/transaksi/stats/hari-ini',   [TransaksiApiController::class, 'statsHariIni'])->name('api.transaksi.stats.hari-ini');
    Route::get('/transaksi/stats/bulan-ini',  [TransaksiApiController::class, 'statsBulanIni'])->name('api.transaksi.stats.bulan-ini');
});
