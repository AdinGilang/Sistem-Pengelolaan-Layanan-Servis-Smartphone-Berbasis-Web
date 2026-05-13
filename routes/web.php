<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServisController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\StatistikController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/cek-servis',  [ServisController::class, 'cekStatus'])->name('servis.cek');
Route::post('/cek-servis', [ServisController::class, 'cekStatus'])->name('servis.cek.status');

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Dashboard — Admin & Owner
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'role:admin|owner'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Servis — Admin & Owner (proteksi aksi di view & controller)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin|owner'])->group(function () {
    Route::post('/servis/{servis}/pin-verify', [ServisController::class, 'pinVerify'])->name('servis.pin.verify');
    Route::get('/servis/{servis}/pin-show',   [ServisController::class, 'pinShow'])  ->name('servis.pin.show');
    Route::get('/servis/{servis}/invoice',       [InvoiceController::class, 'show']) ->name('invoice.show');
    Route::get('/servis/{servis}/invoice/pdf',   [InvoiceController::class, 'pdf'])  ->name('invoice.pdf');
    Route::get('/servis/{servis}/invoice/cetak', [InvoiceController::class, 'cetak'])->name('invoice.cetak');
    Route::resource('servis', ServisController::class)
        ->parameters(['servis' => 'servis']);
});

/*
|--------------------------------------------------------------------------
| Setting — Admin Only
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/setting',  [SettingController::class, 'index']) ->name('setting.index');
    Route::put('/setting',  [SettingController::class, 'update'])->name('setting.update');
});

/*
|--------------------------------------------------------------------------
| Owner Only — Laporan & Statistik
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:owner'])->group(function () {

    Route::get('/laporan',       [LaporanController::class, 'index'])      ->name('laporan.index');
    Route::get('/laporan/excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');
    Route::get('/laporan/pdf',   [LaporanController::class, 'exportPdf'])  ->name('laporan.pdf');
    Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])      ->name('laporan.cetak');

    Route::get('/statistik', [StatistikController::class, 'index'])->name('statistik.index');

});

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile',    [ProfileController::class, 'edit'])   ->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update']) ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';