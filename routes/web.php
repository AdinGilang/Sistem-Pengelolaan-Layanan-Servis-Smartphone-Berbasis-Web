<?php

use App\Http\Controllers\LegalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route inti aplikasi
|--------------------------------------------------------------------------
|
| Berkas ini sengaja dibuat ramping. Seluruh route fitur kini tinggal di
| dalam modulnya masing-masing (app/Modules/<Modul>/routes.php) dan dimuat
| otomatis oleh ModuleServiceProvider, sesuai pola HMVC.
|
| Yang tersisa di sini hanya hal-hal lintas modul: halaman depan, halaman
| legal, sitemap, dan profil pengguna.
|
*/

Route::get('/', \App\Http\Controllers\HomeController::class)->name('home');

// ── Halaman legal — dibutuhkan agar situs layak diindeks mesin pencari ──
Route::get('/kebijakan-privasi', [LegalController::class, 'privasi'])->name('legal.privasi');
Route::get('/syarat-ketentuan', [LegalController::class, 'syarat'])->name('legal.syarat');

// ── SEO ─────────────────────────────────────────────────────────────────
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

// ── Profil pengguna yang sedang masuk ───────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
