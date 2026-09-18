<?php

namespace App\Providers;

use App\Modules\Servis\Models\Servis;
use App\Modules\Servis\Observers\ServisObserver;
use App\Modules\Servis\Policies\ServisPolicy;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Observer: cache laporan otomatis dibuang setiap data servis berubah.
        Servis::observe(ServisObserver::class);

        // Authorization terpusat — dipakai controller lewat $this->authorize()
        // dan view lewat @can, sehingga aturan role hanya ditulis satu kali.
        Gate::policy(Servis::class, ServisPolicy::class);

        $this->enforcePasswordPolicy();
        $this->enforceHttpsInProduction();
        $this->shareSidebarCounter();
    }

    /**
     * Lencana jumlah servis di sidebar.
     *
     * Sebelumnya layout memanggil Servis::count() langsung di dalam Blade,
     * sehingga setiap halaman yang memakai layout itu menembak database satu
     * kali lagi hanya untuk sebuah angka kecil. Sekarang nilainya dibagikan
     * lewat view composer dan di-cache satu menit.
     */
    private function shareSidebarCounter(): void
    {
        View::composer('layouts.app', function ($view): void {
            $view->with('jumlahServis', Cache::remember(
                'layout.jumlah_servis',
                now()->addMinute(),
                static fn (): int => Servis::count(),
            ));
        });
    }

    /**
     * Kebijakan password minimum untuk seluruh aplikasi (register, reset,
     * dan ganti password) — minimal 10 karakter, campuran huruf/angka/simbol,
     * serta ditolak bila pernah bocor di kebocoran data publik.
     */
    private function enforcePasswordPolicy(): void
    {
        Password::defaults(function () {
            $rule = Password::min(10)->letters()->mixedCase()->numbers()->symbols();

            return $this->app->environment('production') ? $rule->uncompromised() : $rule;
        });
    }

    /**
     * Di production seluruh URL yang dihasilkan helper route()/asset()
     * dipaksa memakai skema https agar tidak ada mixed content maupun
     * cookie sesi yang terkirim lewat koneksi polos.
     */
    private function enforceHttpsInProduction(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
