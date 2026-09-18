<?php

use App\Modules\Setting\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

/*
| Pengaturan teks nota hanya boleh disentuh Admin.
*/
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
    Route::put('/setting', [SettingController::class, 'update'])->name('setting.update');
});
