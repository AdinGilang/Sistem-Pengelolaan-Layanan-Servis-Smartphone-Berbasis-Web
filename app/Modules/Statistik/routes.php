<?php

use App\Modules\Statistik\Controllers\StatistikController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/statistik', [StatistikController::class, 'index'])->name('statistik.index');
});
