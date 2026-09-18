<?php

use App\Modules\Servis\Controllers\ServisController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Modul Servis
|--------------------------------------------------------------------------
|
| Pembagian hak akses dibuat eksplisit di level route, lalu diperiksa sekali
| lagi oleh ServisPolicy di dalam controller. Versi lama menaruh seluruh
| resource di bawah satu grup "admin|owner" dan hanya menyembunyikan tombol
| aksi di tampilan, sehingga akun Owner sebenarnya masih bisa membuat,
| mengubah, dan menghapus data lewat request langsung.
|
*/

// ── Publik: pelacakan status oleh pelanggan ─────────────────────────────
// Dibatasi 20 permintaan per menit per IP untuk meredam penebakan kode
// servis secara massal.
Route::get('/cek-servis', [ServisController::class, 'cekStatus'])
    ->middleware('throttle:20,1')
    ->name('servis.cek');

// ── Admin & Owner: hanya membaca ────────────────────────────────────────
Route::middleware(['auth', 'role:admin|owner'])->group(function () {
    Route::get('/servis', [ServisController::class, 'index'])->name('servis.index');
    Route::get('/servis/{servis}', [ServisController::class, 'show'])
        ->whereNumber('servis')
        ->name('servis.show');
});

// ── Admin: mengelola data ───────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/servis/create', [ServisController::class, 'create'])->name('servis.create');
    Route::post('/servis', [ServisController::class, 'store'])->name('servis.store');

    Route::get('/servis/{servis}/edit', [ServisController::class, 'edit'])
        ->whereNumber('servis')
        ->name('servis.edit');

    Route::match(['put', 'patch'], '/servis/{servis}', [ServisController::class, 'update'])
        ->whereNumber('servis')
        ->name('servis.update');

    Route::delete('/servis/{servis}', [ServisController::class, 'destroy'])
        ->whereNumber('servis')
        ->name('servis.destroy');

    // Verifikasi kredensial perangkat — dibatasi ketat karena ruang tebakan
    // PIN hanya 4-6 digit.
    Route::post('/servis/{servis}/pin-verify', [ServisController::class, 'pinVerify'])
        ->whereNumber('servis')
        ->middleware('throttle:10,1')
        ->name('servis.pin.verify');

    Route::get('/servis/{servis}/pin-show', [ServisController::class, 'pinShow'])
        ->whereNumber('servis')
        ->name('servis.pin.show');
});
