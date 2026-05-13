<?php

namespace App\Providers;

use App\Models\Servis;
use App\Observers\ServisObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Daftarkan observer agar cache laporan otomatis dihapus
        // setiap kali data servis dibuat, diupdate, atau dihapus
        Servis::observe(ServisObserver::class);
    }
}