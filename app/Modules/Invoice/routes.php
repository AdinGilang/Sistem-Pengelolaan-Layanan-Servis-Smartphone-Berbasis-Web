<?php

use App\Modules\Invoice\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

/*
| Invoice boleh dilihat dan dicetak Admin maupun Owner, karena keduanya
| memang perlu bukti transaksi. Tidak ada aksi tulis di modul ini.
*/
Route::middleware(['auth', 'role:admin|owner'])
    ->prefix('servis/{servis}')
    ->whereNumber('servis')
    ->group(function () {
        Route::get('/invoice', [InvoiceController::class, 'show'])->name('invoice.show');
        Route::get('/invoice/pdf', [InvoiceController::class, 'pdf'])->name('invoice.pdf');
        Route::get('/invoice/cetak', [InvoiceController::class, 'cetak'])->name('invoice.cetak');
    });
