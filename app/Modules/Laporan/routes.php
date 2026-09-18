<?php

use App\Modules\Laporan\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

/*
| Laporan bersifat khusus Owner: berisi rekap pendapatan yang tidak perlu
| diakses staf operasional.
*/
Route::middleware(['auth', 'role:owner'])->prefix('laporan')->group(function () {
    Route::get('/', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');
    Route::get('/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.pdf');
    Route::get('/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');

    // Aksi yang mengubah state server memakai POST + CSRF, bukan GET.
    Route::post('/segarkan', [LaporanController::class, 'clearCache'])->name('laporan.cache.clear');
});
