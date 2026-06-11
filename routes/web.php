<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\TransaksiBarangController;
use App\Http\Controllers\ScanLogsController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

/* ── AUTH ── */
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

/* ── PROTECTED ── */
Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    /* Barang */
    Route::resource('barang', BarangController::class);
    Route::get('/barang/{kode}/qr',     [BarangController::class, 'downloadQR'])->name('barang.qr');
    Route::get('/barang/{kode}/qr-svg', [BarangController::class, 'qrSvg'])->name('barang.qr.svg');

    /* Kategori & Lokasi */
    Route::resource('kategori', KategoriController::class)->except(['show','create','edit']);
    Route::resource('lokasi',   LokasiController::class)->except(['show','create','edit']);

    /* Transaksi */
    Route::resource('transaksi', TransaksiBarangController::class)->except(['show','create','edit']);

    /* Scan */
    Route::get('/scan-camera', [ScanLogsController::class, 'camera'])->name('scan.camera');
    Route::get('/scan/{kode}', [ScanLogsController::class, 'scan'])->name('barang.scan');
    Route::get('/scan-logs',   [ScanLogsController::class, 'index'])->name('scan.logs');

    /* Laporan & Export */
    Route::get('/laporan',                   [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export',            [LaporanController::class, 'export'])->name('laporan.export');
    Route::get('/laporan/export-barang',     [LaporanController::class, 'exportBarang'])->name('laporan.export.barang');
    Route::get('/laporan/export-transaksi',  [LaporanController::class, 'exportTransaksi'])->name('laporan.export.transaksi');
});
